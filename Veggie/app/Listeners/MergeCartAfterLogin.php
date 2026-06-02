<?php

namespace App\Listeners;

use App\Models\CartItem;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Session;

class MergeCartAfterLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionCart = Session::get('cart', []);
        if(!empty($sessionCart)) {
           foreach($sessionCart as $productId => $cartItem) {
               $existingItem = CartItem::where('user_id', $user->id)
                   ->where('product_id', $productId)
                   ->first();
                if ($existingItem) {
                    $existingItem->quantity += $cartItem['quantity'];
                    $existingItem->save();
                } else {
                    CartItem::create([
                        'user_id' => $user->id,
                        'product_id' => $productId,
                        'quantity' => $cartItem['quantity'],
                    ]);
                }
           }
        }
        Session::forget('cart');// Xóa giỏ hàng trong session sau khi đã hợp nhất
    }
}
