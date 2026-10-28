<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ChatFallbackTest extends TestCase
{
    use RefreshDatabase;

    private Product $salmon;

    private Product $mackerel;

    private Product $tomato;

    private Product $carrot;

    private Product $outOfStockTuna;

    protected function setUp(): void
    {
        parent::setUp();

        // Keep these tests deterministic and exercise the local product fallback.
        config()->set('services.gemini.api_key', '');
        Cache::flush();

        $fish = Category::query()->create([
            'name' => 'Cá tươi',
            'slug' => 'ca-tuoi',
        ]);

        $vegetables = Category::query()->create([
            'name' => 'Rau củ',
            'slug' => 'rau-cu',
        ]);

        $this->salmon = $this->createProduct($fish, [
            'name' => 'Cá hồi Na Uy',
            'slug' => 'ca-hoi-na-uy',
            'price' => 180000,
            'stock' => 8,
        ]);

        $this->mackerel = $this->createProduct($fish, [
            'name' => 'Cá thu tươi',
            'slug' => 'ca-thu-tuoi',
            'price' => 95000,
            'stock' => 5,
        ]);

        $this->tomato = $this->createProduct($vegetables, [
            'name' => 'Cà chua hữu cơ',
            'slug' => 'ca-chua-huu-co',
            'price' => 18000,
            'stock' => 12,
        ]);

        $this->carrot = $this->createProduct($vegetables, [
            'name' => 'Cà rốt Đà Lạt',
            'slug' => 'ca-rot-da-lat',
            'price' => 22000,
            'stock' => 9,
        ]);

        $this->outOfStockTuna = $this->createProduct($fish, [
            'name' => 'Cá ngừ đại dương',
            'slug' => 'ca-ngu-dai-duong',
            'price' => 210000,
            'stock' => 0,
            'status' => 'out_of_stock',
        ]);
    }

    #[DataProvider('fishQueries')]
    public function test_fish_queries_only_return_in_stock_fish(string $query): void
    {
        $reply = $this->sendChatMessage($query);

        $this->assertStringContainsString($this->salmon->name, $reply);
        $this->assertStringContainsString($this->mackerel->name, $reply);
        $this->assertStringNotContainsString($this->tomato->name, $reply);
        $this->assertStringNotContainsString($this->carrot->name, $reply);
        $this->assertStringNotContainsString($this->outOfStockTuna->name, $reply);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function fishQueries(): array
    {
        return [
            'accented Vietnamese' => ['Cho tôi thông tin các loại cá'],
            'unaccented Vietnamese' => ['Cho toi thong tin cac loai ca'],
        ];
    }

    public function test_tomato_query_returns_tomato_without_fish_results(): void
    {
        $reply = $this->sendChatMessage('Tôi muốn mua cà chua hữu cơ');

        $this->assertStringContainsString($this->tomato->name, $reply);
        $this->assertStringNotContainsString($this->salmon->name, $reply);
        $this->assertStringNotContainsString($this->mackerel->name, $reply);
        $this->assertStringNotContainsString($this->outOfStockTuna->name, $reply);
    }

    public function test_out_of_stock_match_is_excluded_without_unrelated_substitutions(): void
    {
        $reply = $this->sendChatMessage('Cho tôi mua cá ngừ đại dương');

        $this->assertStringNotContainsString($this->outOfStockTuna->name, $reply);
        $this->assertStringNotContainsString($this->salmon->name, $reply);
        $this->assertStringNotContainsString($this->mackerel->name, $reply);
        $this->assertStringNotContainsString($this->tomato->name, $reply);
        $this->assertStringNotContainsString($this->carrot->name, $reply);
    }

    public function test_gemini_forbidden_response_uses_the_same_relevant_fallback(): void
    {
        config()->set('services.gemini.api_key', 'test-api-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => ['message' => 'Forbidden'],
            ], 403),
        ]);

        $reply = $this->sendChatMessage('Cá hồi có lợi ích dinh dưỡng gì?');

        $this->assertStringContainsString($this->salmon->name, $reply);
        $this->assertStringNotContainsString($this->mackerel->name, $reply);
        $this->assertStringNotContainsString($this->tomato->name, $reply);
        $this->assertStringNotContainsString($this->carrot->name, $reply);
        Http::assertSentCount(1);
    }

    public function test_gemini_receives_only_relevant_product_context(): void
    {
        config()->set('services.gemini.api_key', 'test-api-key');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [['text' => 'Cá hồi là nguồn đạm tốt.']],
                    ],
                ]],
            ]),
        ]);

        $reply = $this->sendChatMessage('Cá hồi có lợi ích dinh dưỡng gì?');

        $this->assertSame('Cá hồi là nguồn đạm tốt.', $reply);
        Http::assertSent(function ($request) {
            $context = (string) data_get($request->data(), 'system_instruction.parts.0.text');

            return str_contains($context, $this->salmon->name)
                && ! str_contains($context, $this->mackerel->name)
                && ! str_contains($context, $this->tomato->name)
                && ! str_contains($context, $this->carrot->name);
        });
    }

    public function test_guest_history_uses_server_session_instead_of_client_token(): void
    {
        $clientToken = 'guest_'.str_repeat('Z', 40);

        $this->postJson(route('chat.send'), [
            'message' => 'Xin chào',
            'guest_token' => $clientToken,
        ])->assertOk();

        $storedMessage = ChatMessage::query()
            ->where('sender', 'user')
            ->latest('id')
            ->firstOrFail();

        $this->assertNotSame($clientToken, $storedMessage->guest_token);
        $this->assertMatchesRegularExpression('/^guest_[A-Za-z0-9]{40}$/', $storedMessage->guest_token);

        $this->getJson(route('chat.history', ['guest_token' => $clientToken]))
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.0.message', 'Xin chào');
    }

    /**
     * @param  array{name: string, slug: string, price: int, stock: int, status?: string}  $attributes
     */
    private function createProduct(Category $category, array $attributes): Product
    {
        return Product::query()->create([
            ...$attributes,
            'category_id' => $category->id,
            'description' => null,
            'status' => $attributes['status'] ?? 'in_stock',
            'unit' => 'kg',
        ]);
    }

    private function sendChatMessage(string $message): string
    {
        $response = $this->postJson(route('chat.send'), [
            'message' => $message,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('user_message.message', $message)
            ->assertJsonPath('bot_message.sender', 'bot');

        return (string) $response->json('bot_message.message');
    }
}
