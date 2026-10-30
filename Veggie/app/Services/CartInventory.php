<?php

namespace App\Services;

use App\Models\Product;

class CartInventory
{
    public static function issue(?Product $product, int $quantity, ?int $productId = null): ?array
    {
        $stock = max(0, (int) ($product?->stock ?? 0));
        $name = $product?->name ?? 'không còn tồn tại';
        $unit = trim($product?->unit ?? '') ?: 'sản phẩm';

        if (!$product) {
            $code = 'product_unavailable';
            $message = 'Một sản phẩm trong giỏ không còn tồn tại. Vui lòng xóa sản phẩm để tiếp tục.';
        } elseif ($stock === 0) {
            $code = 'out_of_stock';
            $message = "Sản phẩm ‘{$name}’ hiện đã hết hàng.";
        } elseif ($quantity < 1) {
            $code = 'invalid_quantity';
            $message = "Vui lòng chọn số lượng nguyên dương cho sản phẩm ‘{$name}’.";
        } elseif ($quantity > $stock) {
            $code = 'stock_exceeded';
            $message = "Sản phẩm ‘{$name}’ chỉ còn {$stock} {$unit} trong kho. Vui lòng chọn số lượng phù hợp.";
        } else {
            return null;
        }

        return [
            'code' => $code,
            'product_id' => $product?->id ?? $productId,
            'name' => $name,
            'unit' => $unit,
            'stock' => $stock,
            'cart_quantity' => $quantity,
            'message' => $message,
        ];
    }

    public static function issues(iterable $cartItems): array
    {
        $issues = [];
        $groups = collect($cartItems)->groupBy('product_id');

        foreach ($groups as $productId => $items) {
            // Reject invalid legacy rows before summing, so negative quantities cannot hide overselling.
            $invalid = $items->first(fn ($item) => (int) $item->quantity < 1);
            $quantity = $invalid ? (int) $invalid->quantity : (int) $items->sum('quantity');
            $issue = self::issue($items->first()->product, $quantity, (int) $productId);
            if ($issue) {
                $issues[] = $issue;
            }
        }

        return $issues;
    }
}
