<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartInventoryTest extends TestCase
{
    use RefreshDatabase;

    private function product(int $stock): Product
    {
        $category = Category::query()->create(['name' => 'Rau củ', 'slug' => 'rau-cu']);

        return Product::query()->create([
            'name' => 'Cà chua',
            'slug' => 'ca-chua',
            'category_id' => $category->id,
            'description' => 'Cà chua tươi',
            'price' => 25000,
            'stock' => $stock,
            'status' => 'in_stock',
            'unit' => 'kg',
        ]);
    }

    public function test_guest_cannot_add_more_than_the_cumulative_stock(): void
    {
        $product = $this->product(3);

        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 2])
            ->assertOk()
            ->assertJsonPath('quantity', 2);

        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 2])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'stock_exceeded')
            ->assertJsonPath('stock', 3)
            ->assertJsonPath('cart_quantity', 2)
            ->assertJsonPath('can_checkout', true);

        $this->assertSame(2, session('cart')[$product->id]['quantity']);
    }

    public function test_guest_quantity_must_be_a_positive_integer(): void
    {
        $product = $this->product(3);

        foreach (['1.5', '0', '-1', 'not-a-number'] as $quantity) {
            $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => $quantity])
                ->assertUnprocessable()
                ->assertJsonPath('code', 'invalid_quantity');
            $this->assertArrayNotHasKey($product->id, session('cart', []));
        }
    }

    public function test_existing_cart_quantity_is_preserved_when_stock_drops(): void
    {
        $product = $this->product(3);

        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 3])->assertOk();
        $product->update(['stock' => 1]);

        $this->postJson('/cart/update', ['product_id' => $product->id, 'quantity' => 3])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'stock_exceeded')
            ->assertJsonPath('cart_quantity', 3)
            ->assertJsonPath('can_checkout', false);

        $this->assertSame(3, session('cart')[$product->id]['quantity']);
    }

    public function test_sold_out_item_stays_in_cart_and_disables_quantity_controls(): void
    {
        $product = $this->product(1);
        $this->postJson('/cart/add', ['product_id' => $product->id, 'quantity' => 1])->assertOk();
        $product->update(['stock' => 0]);

        $this->get('/cart')
            ->assertOk()
            ->assertSee('Hết hàng')
            ->assertSee('cart-item-out-of-stock', false)
            ->assertSee('disabled', false)
            ->assertSee('data-cart-checkout-blocked="1"', false);
    }
}
