<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Get chat history.
     */
    public function getHistory(Request $request)
    {
        $userId = Auth::user()?->id;
        $guestToken = $request->query('guest_token');

        if ($userId) {
            $messages = ChatMessage::where('user_id', $userId)
                ->orderBy('created_at', 'asc')
                ->get();
        } elseif ($guestToken) {
            $messages = ChatMessage::where('guest_token', $guestToken)
                ->whereNull('user_id')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $messages = [];
        }

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    /**
     * Send user message and generate bot reply.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'guest_token' => 'nullable|string|max:100'
        ]);

        $userId = Auth::user()?->id;
        $guestToken = $request->input('guest_token');
        $userText = $request->input('message');

        // 1. Save user message
        $userMessage = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'user',
            'message' => $userText
        ]);

        // 2. Generate bot response via Gemini API
        $botReplyText = $this->callGeminiApi($userText, $userId, $guestToken, $userMessage->id);

        // 3. Save bot response
        $botMessage = ChatMessage::create([
            'user_id' => $userId,
            'guest_token' => $userId ? null : $guestToken,
            'sender' => 'bot',
            'message' => $botReplyText
        ]);

        return response()->json([
            'status' => 'success',
            'user_message' => $userMessage,
            'bot_message' => $botMessage
        ]);
    }

    /**
     * Call Gemini API with product context from database.
     */
    private function callGeminiApi(
        string $userText,
        ?int $userId,
        ?string $guestToken,
        ?int $excludeMessageId = null
    ): string
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
            return $this->fallbackReply($userText);
        }

        try {
            // Build product context from database
            $productContext = $this->buildProductContext($userText);

            // Build conversation history for context (last 10 messages)
            $chatHistory = $this->getChatHistory($userId, $guestToken, 10, $excludeMessageId);

            // System instruction for the bot
            $systemInstruction = "Bạn là trợ lý bán hàng AI của cửa hàng Veggie – chuyên cung cấp rau củ quả, thịt, cá và nông sản hữu cơ chất lượng cao.\n\n"
                . "QUY TẮC:\n"
                . "- Luôn trả lời bằng tiếng Việt, thân thiện, ngắn gọn.\n"
                . "- Chỉ tư vấn về sản phẩm, giá cả, giao hàng, khuyến mãi của Veggie.\n"
                . "- Nếu câu hỏi không liên quan đến cửa hàng, lịch sự từ chối và hướng dẫn khách hỏi về sản phẩm.\n"
                . "- Khi trả lời về sản phẩm, dùng thông tin từ DANH SÁCH SẢN PHẨM bên dưới.\n"
                . "- Định dạng giá tiền bằng VNĐ với dấu chấm phân cách hàng nghìn.\n"
                . "- Dùng emoji phù hợp để tạo cảm giác thân thiện.\n"
                . "- Phí giao hàng: 15.000 VNĐ. Miễn phí ship cho đơn từ 300.000 VNĐ.\n"
                . "- Giao hàng nội thành trong 2 giờ.\n\n"
                . "DANH SÁCH SẢN PHẨM HIỆN CÓ:\n"
                . $productContext;

            // Build messages array for Gemini
            $contents = [];

            // Add chat history as context
            foreach ($chatHistory as $msg) {
                $contents[] = [
                    'role' => $msg->sender === 'user' ? 'user' : 'model',
                    'parts' => [['text' => $msg->message]]
                ];
            }

            // Add current user message
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $userText]]
            ];

            // Call Gemini API
            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
            ])->connectTimeout(3)->timeout(10)->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
                [
                    'system_instruction' => [
                        'parts' => [['text' => $systemInstruction]]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    return trim($text);
                }
            }

            Log::warning('Gemini API response error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return $this->fallbackReply($userText);
        } catch (\Exception $e) {
            Log::error('Gemini API call failed', [
                'error' => $e->getMessage()
            ]);

            return $this->fallbackReply($userText);
        }
    }

    /**
     * Build product context string from database.
     */
    private function buildProductContext(string $userText): string
    {
        // Get all in-stock products grouped by category
        $products = Product::with('category')
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->orderBy('category_id')
            ->get();

        if ($products->isEmpty()) {
            return "Hiện tại chưa có sản phẩm nào trong kho.\n";
        }

        $context = "";
        $currentCategory = null;

        foreach ($products as $product) {
            $categoryName = $product->category->name ?? 'Khác';

            if ($categoryName !== $currentCategory) {
                $context .= "\n[{$categoryName}]\n";
                $currentCategory = $categoryName;
            }

            $context .= "- {$product->name}: " . number_format($product->price, 0, ',', '.') . " VNĐ/{$product->unit} (còn {$product->stock})\n";
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
    )
    {
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

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values();
    }

    /**
     * Fallback reply when Gemini API is unavailable.
     */
    private function fallbackReply(string $userText): string
    {
        $text = Str::lower(Str::ascii($userText));

        if (preg_match('/\b(xin chao|chao|hello|hi)\b/u', $text)) {
            return 'Xin chào! Chúc bạn một ngày tốt lành. Tôi có thể hỗ trợ gì cho bạn về các sản phẩm rau củ quả hữu cơ của Veggie? 🥬';
        }

        if ($this->isGeneralSupportQuestion($text)) {
            return "Mình có thể hỗ trợ bạn về:\n"
                . "• Sản phẩm, giá và số lượng còn lại\n"
                . "• Giao hàng và phí vận chuyển\n"
                . "• Khuyến mãi\n"
                . "• Cách đặt hàng và thanh toán\n"
                . "Bạn muốn tìm hiểu nội dung nào trước? 🌿";
        }

        if (str_contains($text, 'thanh toan') || str_contains($text, 'paypal')) {
            return 'Veggie hiện hỗ trợ thanh toán khi nhận hàng (COD) và thanh toán trực tuyến qua PayPal nếu phương thức này hiển thị ở bước thanh toán. Bạn chỉ cần thêm sản phẩm vào giỏ, nhập địa chỉ nhận hàng rồi chọn phương thức thanh toán phù hợp nhé! 💳';
        }

        if (str_contains($text, 'dat hang') || str_contains($text, 'mua hang') || str_contains($text, 'don hang')) {
            return 'Để đặt hàng, bạn hãy thêm sản phẩm vào giỏ hàng, mở Giỏ hàng, kiểm tra số lượng, nhập địa chỉ nhận hàng và bấm Đặt hàng. Sau đó chọn phương thức thanh toán để hoàn tất nhé! 🛒';
        }

        if (str_contains($text, 'ship') || str_contains($text, 'giao hang') || str_contains($text, 'van chuyen')) {
            return 'Veggie giao hàng nhanh nội thành trong vòng 2 giờ. Phí ship 15.000 VNĐ, miễn phí cho đơn từ 300.000 VNĐ! 🚚';
        }

        if (str_contains($text, 'khuyen mai') || str_contains($text, 'uu dai') || str_contains($text, 'giam gia')) {
            return 'Bạn có thể hỏi mình tên sản phẩm hoặc xem mục Khuyến mãi trên cửa hàng để biết ưu đãi hiện có nhé! 🎁';
        }

        $matchingProducts = $this->findMatchingProducts($userText);

        if ($matchingProducts->isNotEmpty()) {
            $reply = "Mình tìm thấy các sản phẩm phù hợp:\n";

            foreach ($matchingProducts as $product) {
                $reply .= "• {$product->name}: "
                    . number_format($product->price, 0, ',', '.')
                    . ' VNĐ/' . ($product->unit ?? 'sản phẩm')
                    . " (còn {$product->stock})\n";
            }

            return trim($reply) . "\nBạn muốn mình tư vấn thêm sản phẩm nào? 🌿";
        }

        if (str_contains($text, 'gia') || str_contains($text, 'bao nhieu') || str_contains($text, 'tien')) {
            $products = Product::where('status', 'in_stock')->take(3)->get();
            if ($products->count() > 0) {
                $reply = "Dưới đây là một số sản phẩm tiêu biểu:\n";
                foreach ($products as $product) {
                    $reply .= "• " . $product->name . ": " . number_format($product->price, 0, ',', '.') . " VNĐ/" . ($product->unit ?? 'kg') . "\n";
                }
                $reply .= "Hãy gõ tên sản phẩm cụ thể để xem giá nhé!";
                return $reply;
            }
        }

        return 'Cảm ơn bạn đã liên hệ Veggie! Hiện tại hệ thống AI đang bảo trì, vui lòng thử lại sau hoặc gọi Hotline: 1900-VEGGIE nhé! 💚';
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

    /**
     * Find relevant in-stock products for a useful offline fallback.
     */
    private function findMatchingProducts(string $userText)
    {
        $normalizedText = preg_replace('/[^a-z0-9]+/u', ' ', Str::lower(Str::ascii($userText))) ?? '';
        $stopWords = [
            'ban', 'biet', 'cac', 'can', 'cho', 'co', 'cua', 'duoc', 'gi', 'giup', 'hay',
            'hieu', 'la', 'loai', 'minh', 'mot', 'muon', 'nao', 'nay', 'pham', 'san',
            'the', 'thong', 'tin', 'tim', 'toi', 've', 'voi', 'xin', 'nhung',
        ];
        $keywords = collect(preg_split('/\s+/u', trim($normalizedText)) ?: [])
            ->filter(fn ($word) => strlen($word) >= 2 && !in_array($word, $stopWords, true))
            ->unique()
            ->values();

        if ($keywords->isEmpty()) {
            return collect();
        }

        $matches = Product::with('category')
            ->where('status', 'in_stock')
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($product) use ($keywords) {
                $searchableText = $product->name . ' ' . ($product->category?->name ?? '');
                $normalizedName = Str::lower(Str::ascii($searchableText));
                $score = $keywords->sum(
                    fn ($keyword) => str_contains($normalizedName, $keyword) ? strlen($keyword) : 0
                );

                return ['product' => $product, 'score' => $score];
            })
            ->filter(fn ($match) => $match['score'] > 0)
            ->sortByDesc('score');

        if ($matches->isEmpty()) {
            return collect();
        }

        $minimumScore = (int) ceil($matches->max('score') * 0.7);

        return $matches
            ->filter(fn ($match) => $match['score'] >= $minimumScore)
            ->take(3)
            ->pluck('product')
            ->values();

        }
}
