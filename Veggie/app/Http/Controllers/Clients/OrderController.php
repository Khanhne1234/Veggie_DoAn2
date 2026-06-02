<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function ShowOrders($id)
    {
        $order = Order::with(['orderItems.product.firstImage', 'user', 'shippingAddress', 'payment'])->findOrFail($id);

        $userId = Auth::id();
        return view('clients.pages.order_detail', compact('order'));
    }

    public function cancelOrder($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }
        //update order status to canceled
        $order->update(['status' => 'cancelled']);
        return redirect()->back()->with('toastr', [
            'type' => 'success',
            'message' => 'Đơn hàng đã được hủy thành công.'
        ]);
    }
}
