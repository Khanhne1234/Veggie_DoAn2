<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'message' => 'required|string',
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
        $botReplyText = $this->callGeminiApi($userText, $userId, $guestToken);

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
    private function callGeminiApi(string $userText, ?int $userId, ?string $guestToken): string
    {
        $apiKey = config('services.gemini.api_key');

        if (!$apiKey || $apiKey === 'your_gemini_api_key_here') {
            return $this->fallbackReply($userText);
        }

        try {
            // Build product context from database
            $productContext = $this->buildProductContext($userText);

            // Build conversation history for context (last 10 messages)
            $chatHistory = $this->getChatHistory($userId, $guestToken, 10);

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
            $response = Http::timeout(45)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}",
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
    private function getChatHistory(?int $userId, ?string $guestToken, int $limit = 10)
    {
        if ($userId) {
            return ChatMessage::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->reverse()
                ->values();
        }

        if ($guestToken) {
            return ChatMessage::where('guest_token', $guestToken)
                ->whereNull('user_id')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->reverse()
                ->values();
        }

        return collect();
    }

    /**
     * Fallback reply when Gemini API is unavailable.
     */
    private function fallbackReply(string $userText): string
    {
        $text = mb_strtolower($userText, 'UTF-8');

        if (str_contains($text, 'xin chào') || str_contains($text, 'hello') || str_contains($text, 'hi') || str_contains($text, 'chào')) {
            return 'Xin chào! Chúc bạn một ngày tốt lành. Tôi có thể hỗ trợ gì cho bạn về các sản phẩm rau củ quả hữu cơ của Veggie? 🥬';
        }

        if (str_contains($text, 'giá') || str_contains($text, 'bao nhiêu') || str_contains($text, 'tiền')) {
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

        if (str_contains($text, 'ship') || str_contains($text, 'giao hàng') || str_contains($text, 'vận chuyển')) {
            return 'Veggie giao hàng nhanh nội thành trong vòng 2 giờ. Phí ship 15.000 VNĐ, miễn phí cho đơn từ 300.000 VNĐ! 🚚';
        }

        return 'Cảm ơn bạn đã liên hệ Veggie! Hiện tại hệ thống AI đang bảo trì, vui lòng thử lại sau hoặc gọi Hotline: 1900-VEGGIE nhé! 💚';
    }
}
