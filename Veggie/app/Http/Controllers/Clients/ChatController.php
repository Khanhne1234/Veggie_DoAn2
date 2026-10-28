<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    private const HISTORY_LIMIT = 50;

    private const GEMINI_FAILURE_CACHE_KEY = 'chat.gemini_temporarily_unavailable';

    /**
     * Get chat history.
     */
    public function getHistory(Request $request)
    {
        $userId = Auth::user()?->id;
        $guestToken = $this->resolveGuestToken($request, $userId);

        if ($userId) {
            $messages = ChatMessage::where('user_id', $userId)
                ->orderByDesc('id')
                ->limit(self::HISTORY_LIMIT)
                ->get()
                ->reverse()
                ->values();
        } elseif ($guestToken) {
            $messages = ChatMessage::where('guest_token', $guestToken)
                ->whereNull('user_id')
                ->orderByDesc('id')
                ->limit(self::HISTORY_LIMIT)
                ->get()
                ->reverse()
                ->values();
        } else {
            $messages = collect();
        }

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }

    /**
     * Send user message and generate bot reply.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userId = Auth::user()?->id;
        $guestToken = $this->resolveGuestToken($request, $userId);
        $userText = trim($request->string('message')->toString());

        // 1. Save user message
        $userMessage = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $userText,
        ]);

        // 2. Prefer deterministic store answers; use Gemini for open-ended advice.
        $reply = $this->generateReply($userText, $userId, $guestToken, $userMessage->id);

        // 3. Save bot response
        $botMessage = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $reply['text'],
        ]);

        return response()->json([
            'status' => 'success',
            'user_message' => $userMessage,
            'bot_message' => $botMessage,
            'reply_source' => $reply['source'],
            'degraded' => $reply['source'] === 'fallback',
        ]);
    }

    /**
     * Generate a store answer locally when possible, otherwise call Gemini.
     */
    private function generateReply(
        string $userText,
        ?int $userId,
        ?string $guestToken,
        ?int $excludeMessageId = null
    ): array {
        $localReply = $this->buildLocalReply($userText);

        if ($localReply !== null) {
            return ['text' => $localReply, 'source' => 'local'];
        }

        $apiKey = config('services.gemini.api_key');

        if (! $apiKey || $apiKey === 'your_gemini_api_key_here' || $this->isGeminiTemporarilyUnavailable()) {
            return ['text' => $this->fallbackReply($userText), 'source' => 'fallback'];
        }

        try {
            // Build product context from database
            $productContext = $this->buildProductContext($userText);

            // Build conversation history for context (last 10 messages)
            $chatHistory = $this->getChatHistory($userId, $guestToken, 10, $excludeMessageId);

            // System instruction for the bot
            $systemInstruction = "Bạn là trợ lý bán hàng AI của cửa hàng Veggie – chuyên cung cấp rau củ quả, thịt, cá và nông sản hữu cơ chất lượng cao.\n\n"
                ."QUY TẮC:\n"
                ."- Luôn trả lời bằng tiếng Việt, thân thiện, ngắn gọn.\n"
                ."- Chỉ tư vấn về sản phẩm, giá cả, giao hàng, khuyến mãi của Veggie.\n"
                ."- Nếu câu hỏi không liên quan đến cửa hàng, lịch sự từ chối và hướng dẫn khách hỏi về sản phẩm.\n"
                ."- Khi trả lời về sản phẩm, dùng thông tin từ DANH SÁCH SẢN PHẨM bên dưới.\n"
                ."- Định dạng giá tiền bằng VNĐ với dấu chấm phân cách hàng nghìn.\n"
                ."- Dùng emoji phù hợp để tạo cảm giác thân thiện.\n"
                ."- Phí giao hàng: 15.000 VNĐ. Miễn phí ship cho đơn từ 300.000 VNĐ.\n"
                ."- Giao hàng nội thành trong 2 giờ.\n"
                ."- Chỉ trả lời đúng sản phẩm hoặc danh mục khách đang hỏi; không thay thế bằng danh mục khác.\n"
                ."- Nếu không có sản phẩm phù hợp trong danh sách, hãy nói rõ là cửa hàng chưa có hoặc đang hết hàng.\n\n"
                ."DANH SÁCH SẢN PHẨM HIỆN CÓ:\n"
                .$productContext;

            // Build messages array for Gemini
            $contents = [];

            // Add chat history as context
            foreach ($chatHistory as $msg) {
                $contents[] = [
                    'role' => $msg->sender === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg->message]],
                ];
            }

            // Add current user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userText]],
            ];

            // Call Gemini API
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])->connectTimeout(3)->timeout(10)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]],
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.2,
                        'maxOutputTokens' => 2048,
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    $this->clearGeminiFailure();

                    return ['text' => trim($text), 'source' => 'gemini'];
                }
            }

            Log::warning('Gemini API response error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $this->markGeminiFailure();

            return ['text' => $this->fallbackReply($userText), 'source' => 'fallback'];
        } catch (\Exception $e) {
            Log::error('Gemini API call failed', [
                'error' => $e->getMessage(),
            ]);

            $this->markGeminiFailure();

            return ['text' => $this->fallbackReply($userText), 'source' => 'fallback'];
        }
    }

    /**
     * Build product context string from database.
     */
    private function buildProductContext(string $userText): string
    {
        $products = $this->findMatchingProducts($userText, 15);

        if ($products->isEmpty()) {
            $products = Product::with('category')
                ->where('status', 'in_stock')
                ->where('stock', '>', 0)
                ->orderBy('category_id')
                ->orderBy('id')
                ->limit(50)
                ->get();
        }

        if ($products->isEmpty()) {
            return "Hiện tại chưa có sản phẩm nào trong kho.\n";
        }

        $context = '';
        $currentCategory = null;

        foreach ($products as $product) {
            $categoryName = $product->category->name ?? 'Khác';

            if ($categoryName !== $currentCategory) {
                $context .= "\n[{$categoryName}]\n";
                $currentCategory = $categoryName;
            }

            $context .= "- {$product->name}: ".number_format($product->price, 0, ',', '.')." VNĐ/{$product->unit} (còn {$product->stock})\n";
        }

        return $context;
    }

    /**
     * Get recent chat history for conversation context.
     */
    private function getChatHistory(
        ?int $userId,
        ?string $guestToken,
        int $limit = 10,
        ?int $excludeMessageId = null
    ) {
        $query = ChatMessage::query();

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($guestToken) {
            $query->where('guest_token', $guestToken)
                ->whereNull('user_id');
        } else {
            return collect();
        }

        if ($excludeMessageId) {
            $query->where('id', '!=', $excludeMessageId);
        }

        return $query->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Keep guest history bound to the Laravel session instead of trusting
     * a client-provided token that could be reused to read another chat.
     */
    private function resolveGuestToken(Request $request, ?int $userId): ?string
    {
        if ($userId) {
            return null;
        }

        $token = $request->session()->get('chat_guest_token');

        if (! is_string($token) || ! preg_match('/^guest_[A-Za-z0-9]{40}$/', $token)) {
            $token = 'guest_'.Str::random(40);
            $request->session()->put('chat_guest_token', $token);
        }

        return $token;
    }

    private function isGeminiTemporarilyUnavailable(): bool
    {
        try {
            return (bool) Cache::store($this->geminiCacheStore())->get(self::GEMINI_FAILURE_CACHE_KEY);
        } catch (\Throwable) {
            return false;
        }
    }

    private function markGeminiFailure(): void
    {
        try {
            Cache::store($this->geminiCacheStore())
                ->put(self::GEMINI_FAILURE_CACHE_KEY, true, now()->addMinutes(5));
        } catch (\Throwable) {
            // The reply fallback still works even if no cache store is available.
        }
    }

    private function clearGeminiFailure(): void
    {
        try {
            Cache::store($this->geminiCacheStore())->forget(self::GEMINI_FAILURE_CACHE_KEY);
        } catch (\Throwable) {
            // A stale marker expires automatically after five minutes.
        }
    }

    private function geminiCacheStore(): string
    {
        $defaultStore = (string) config('cache.default', 'file');

        return $defaultStore === 'database' ? 'file' : $defaultStore;
    }

    /**
     * Build deterministic answers for store policies and catalog lookups.
     */
    private function buildLocalReply(string $userText, bool $allowBroadProductMatch = false): ?string
    {
        $text = Str::lower(Str::ascii($userText));

        if (preg_match('/\b(xin chao|chao|hello|hi)\b/u', $text)) {
            return 'Xin chào! Chúc bạn một ngày tốt lành. Mình có thể hỗ trợ bạn tìm rau củ, trái cây, thịt, cá và các sản phẩm khác của Veggie nhé!';
        }

        if ($this->isGeneralSupportQuestion($text)) {
            return "Mình có thể hỗ trợ bạn về:\n"
                ."• Sản phẩm, giá và số lượng còn lại\n"
                ."• Giao hàng và phí vận chuyển\n"
                ."• Khuyến mãi\n"
                ."• Cách đặt hàng và thanh toán\n"
                .'Bạn muốn tìm hiểu nội dung nào trước?';
        }

        if (str_contains($text, 'thanh toan') || str_contains($text, 'paypal')) {
            return 'Veggie hiện hỗ trợ thanh toán khi nhận hàng (COD) và thanh toán trực tuyến qua PayPal nếu phương thức này hiển thị ở bước thanh toán. Bạn chỉ cần thêm sản phẩm vào giỏ, nhập địa chỉ nhận hàng rồi chọn phương thức thanh toán phù hợp nhé! 💳';
        }

        if (str_contains($text, 'dat hang') || str_contains($text, 'mua hang') || str_contains($text, 'don hang')) {
            return 'Để đặt hàng, bạn hãy thêm sản phẩm vào giỏ hàng, mở Giỏ hàng, kiểm tra số lượng, nhập địa chỉ nhận hàng và bấm Đặt hàng. Sau đó chọn phương thức thanh toán để hoàn tất nhé!';
        }

        if (str_contains($text, 'ship') || str_contains($text, 'giao hang') || str_contains($text, 'van chuyen')) {
            return 'Veggie giao hàng nhanh nội thành trong vòng 2 giờ. Phí ship 15.000 VNĐ, miễn phí cho đơn từ 300.000 VNĐ!';
        }

        if (str_contains($text, 'khuyen mai') || str_contains($text, 'uu dai') || str_contains($text, 'giam gia')) {
            return 'Bạn có thể hỏi mình tên sản phẩm hoặc xem mục Khuyến mãi trên cửa hàng để biết ưu đãi hiện có nhé!';
        }

        $isCatalogLookup = $allowBroadProductMatch || $this->isCatalogLookup($text, $userText);

        if ($isCatalogLookup) {
            $keywords = $this->tokenizeForSearch($userText, true);
            $requestedCategory = $this->findRequestedCategory($keywords);
            $onlyUnavailable = $this->hasOnlyUnavailableExactMatches($keywords);
            $matchingProducts = $this->findMatchingProducts($userText);

            if ($matchingProducts->isNotEmpty()) {
                $reply = "Mình tìm thấy các sản phẩm phù hợp:\n";

                foreach ($matchingProducts as $product) {
                    $reply .= "• {$product->name}: "
                        .number_format($product->price, 0, ',', '.')
                        .' VNĐ/'.($product->unit ?? 'sản phẩm')
                        ." (còn {$product->stock})\n";
                }

                return trim($reply)."\nBạn muốn mình tư vấn thêm sản phẩm nào?";
            }

            if ($onlyUnavailable) {
                return 'Sản phẩm bạn hỏi hiện đang tạm hết hàng. Mình sẽ không tự thay bằng sản phẩm khác để tránh tư vấn sai nhé! 🌿';
            }

            if ($requestedCategory) {
                return "Danh mục {$requestedCategory->name} hiện đang tạm hết hàng. Bạn vui lòng quay lại sau nhé! 🌿";
            }
        }

        if (str_contains($text, 'gia') || str_contains($text, 'bao nhieu') || str_contains($text, 'tien')) {
            return 'Mình chưa tìm thấy sản phẩm bạn muốn hỏi giá. Bạn vui lòng nhập rõ tên sản phẩm hoặc danh mục nhé! 🌿';
        }

        return null;
    }

    /**
     * Fallback reply when Gemini is unavailable.
     */
    private function fallbackReply(string $userText): string
    {
        return $this->buildLocalReply($userText, true)
            ?? 'Cảm ơn bạn đã liên hệ Veggie! Hiện tại hệ thống AI đang bảo trì, vui lòng thử lại sau hoặc gọi Hotline: 1900-VEGGIE nhé! 💚';
    }

    /**
     * Detect broad capability questions before matching product keywords.
     */
    private function isGeneralSupportQuestion(string $text): bool
    {
        return str_contains($text, 'thong tin gi')
            || str_contains($text, 'co the giup gi')
            || str_contains($text, 'giup duoc gi')
            || str_contains($text, 'ho tro gi')
            || str_contains($text, 'tu van gi')
            || (str_contains($text, 'tim hieu') && str_contains($text, 'thong tin'));
    }

    private function isCatalogLookup(string $asciiText, string $originalText): bool
    {
        $catalogPhrases = [
            'bao nhieu', 'ban co', 'con hang', 'danh sach', 'gia', 'loai',
            'mua', 'san pham', 'shop co', 'thong tin', 'tim',
        ];

        foreach ($catalogPhrases as $phrase) {
            if (str_contains($asciiText, $phrase)) {
                return true;
            }
        }

        return count($this->tokenizeForSearch($originalText, true)) <= 2;
    }

    /**
     * Find relevant in-stock products for a useful offline fallback.
     */
    private function findMatchingProducts(string $userText, int $limit = 5)
    {
        $keywords = $this->tokenizeForSearch($userText, true);

        if ($keywords === []) {
            return collect();
        }

        $requestedCategory = $this->findRequestedCategory($keywords);

        if ($requestedCategory) {
            return Product::with('category')
                ->where('category_id', $requestedCategory->id)
                ->where('status', 'in_stock')
                ->where('stock', '>', 0)
                ->orderBy('id')
                ->limit($limit)
                ->get();
        }

        if ($this->hasOnlyUnavailableExactMatches($keywords)) {
            return collect();
        }

        $matches = Product::with('category')
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->orderBy('id')
            ->get()
            ->map(function ($product) use ($keywords) {
                $nameTokens = $this->tokenizeForSearch($product->name);
                $categoryTokens = $this->tokenizeForSearch($product->category?->name ?? '');
                $nameScore = $this->matchingTokenScore($keywords, $nameTokens);
                $categoryScore = $this->matchingTokenScore($keywords, $categoryTokens);
                $score = ($nameScore * 4) + ($categoryScore * 2);

                if ($this->allQueryTokensMatch($keywords, $nameTokens)) {
                    $score += 100;
                } elseif ($this->allQueryTokensMatch($keywords, [...$nameTokens, ...$categoryTokens])) {
                    $score += 50;
                }

                return ['product' => $product, 'score' => $score];
            })
            ->filter(fn ($match) => $match['score'] > 0)
            ->sort(function ($left, $right) {
                return ($right['score'] <=> $left['score'])
                    ?: ($left['product']->id <=> $right['product']->id);
            });

        if ($matches->isEmpty()) {
            return collect();
        }

        $minimumScore = (int) ceil($matches->max('score') * 0.7);

        return $matches
            ->filter(fn ($match) => $match['score'] >= $minimumScore)
            ->take($limit)
            ->pluck('product')
            ->values();
    }

    /**
     * Tokenize text while retaining accents. Each query token also keeps an
     * ASCII form so unaccented input remains searchable without merging
     * accented words such as the Vietnamese words for fish and tomato.
     */
    private function tokenizeForSearch(string $text, bool $removeStopWords = false): array
    {
        $normalized = preg_replace('/[^\p{L}\p{N}]+/u', ' ', Str::lower(trim($text))) ?? '';
        $words = preg_split('/\s+/u', trim($normalized)) ?: [];
        $stopWords = [
            'ai', 'anh', 'ban', 'bao', 'biet', 'cac', 'can', 'cho', 'co', 'con', 'cua',
            'danh', 'duoc', 'em', 'gi', 'gia', 'giup', 'hang', 'hay', 'hien', 'hieu',
            'la', 'loai', 'minh', 'mot', 'mua', 'muon', 'nao', 'nay', 'nhieu', 'nhung',
            'pham', 'san', 'shop', 'so', 'the', 'thong', 'tin', 'tim', 'toi', 've', 'voi',
            'xem', 'xin',
        ];
        $tokens = [];

        foreach ($words as $word) {
            $ascii = preg_replace('/[^a-z0-9]+/', '', Str::lower(Str::ascii($word))) ?? '';

            if (strlen($ascii) < 2 || ($removeStopWords && in_array($ascii, $stopWords, true))) {
                continue;
            }

            $key = $word.'|'.$ascii;
            $tokens[$key] = ['raw' => $word, 'ascii' => $ascii];
        }

        return array_values($tokens);
    }

    private function tokenMatches(array $queryToken, array $targetToken): bool
    {
        if ($queryToken['raw'] !== $queryToken['ascii']) {
            return $queryToken['raw'] === $targetToken['raw'];
        }

        return $queryToken['ascii'] === $targetToken['ascii'];
    }

    private function allQueryTokensMatch(array $queryTokens, array $targetTokens): bool
    {
        if ($queryTokens === []) {
            return false;
        }

        foreach ($queryTokens as $queryToken) {
            $matched = false;

            foreach ($targetTokens as $targetToken) {
                if ($this->tokenMatches($queryToken, $targetToken)) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                return false;
            }
        }

        return true;
    }

    private function matchingTokenScore(array $queryTokens, array $targetTokens): int
    {
        $score = 0;

        foreach ($queryTokens as $queryToken) {
            foreach ($targetTokens as $targetToken) {
                if ($this->tokenMatches($queryToken, $targetToken)) {
                    $score += strlen($queryToken['ascii']);
                    break;
                }
            }
        }

        return $score;
    }

    private function findRequestedCategory(array $queryTokens): ?Category
    {
        return Category::query()
            ->orderBy('id')
            ->get()
            ->filter(function (Category $category) use ($queryTokens) {
                return $this->allQueryTokensMatch(
                    $queryTokens,
                    $this->tokenizeForSearch($category->name)
                );
            })
            ->sortBy(function (Category $category) {
                return count($this->tokenizeForSearch($category->name));
            })
            ->first();
    }

    private function hasOnlyUnavailableExactMatches(array $queryTokens): bool
    {
        $exactMatches = Product::query()
            ->get()
            ->filter(function (Product $product) use ($queryTokens) {
                return $this->allQueryTokensMatch(
                    $queryTokens,
                    $this->tokenizeForSearch($product->name)
                );
            });

        return $exactMatches->isNotEmpty()
            && $exactMatches->every(function (Product $product) {
                return $product->status !== 'in_stock' || $product->stock <= 0;
            });
    }
}
