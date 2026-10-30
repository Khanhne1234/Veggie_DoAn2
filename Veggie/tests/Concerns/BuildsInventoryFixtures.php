<?php

namespace Tests\Concerns;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\ShippingAddress;
use App\Models\User;

trait BuildsInventoryFixtures
{
    private function createInventoryProduct(int $stock = 5, int $price = 10000): Product
    {
        $category = Category::firstOrCreate(['slug' => 'rau-cu'], ['name' => 'Rau củ']);

        return Product::create([
            'name' => 'Cà chua '.(Product::count() + 1),
            'slug' => 'ca-chua-'.(Product::count() + 1),
            'category_id' => $category->id,
            'price' => $price,
            'stock' => $stock,
            // Stock must be authoritative even when this legacy flag is stale.
            'status' => 'in_stock',
            'unit' => 'kg',
        ]);
    }

    private function createInventoryCustomer(): User
    {
        $role = Role::firstOrCreate(['name' => 'customer']);

        return User::create([
            'name' => 'Khách hàng kiểm thử',
            'email' => 'customer-'.(User::count() + 1).'@example.test',
            'password' => 'unused-test-password',
            'status' => 'active',
            'role_id' => $role->id,
        ]);
    }

    private function createInventoryAddress(User $user): ShippingAddress
    {
        return ShippingAddress::create([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'phone' => '0900000000',
            'address' => '123 Đường kiểm thử',
            'city' => 'Hà Nội',
            'default' => true,
        ]);
    }

    private function putInventoryCart(Product $product, int $quantity, ?User $user = null): void
    {
        if ($user) {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);

            return;
        }

        $cart = session()->get('cart', []);
        $cart[$product->id] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
            'price' => $product->price,
            'stock' => $product->stock,
            'image' => 'upload/products/default_product.webp',
        ];
        $this->withSession(['cart' => $cart]);
    }

    private function assertInventoryCartQuantity(Product $product, int $quantity, ?User $user = null): void
    {
        if ($user) {
            $this->assertDatabaseHas('cart_items', [
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);

            return;
        }

        $this->assertSame($quantity, session('cart.'.$product->id.'.quantity'));
    }
}
