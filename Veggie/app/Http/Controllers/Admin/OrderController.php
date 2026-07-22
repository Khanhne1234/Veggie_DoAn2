<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class OrderController extends Controller
{
    public function index()
    {
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->get();
        return view('admin.pages.orders', compact('order'));
    }

    public function confirm(Request $request)
    {
        try {
            $order = Order::findOrFail($request->id);
            $order->status = 'processing';
            $order->save();
            return response()->json([
                'success' => true,
                'message' => 'Xác nhận đơn hàng thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra'
            ]);
        }
    }

    public function showOrderDetail($id)
    {
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->findOrFail($id);
        return view('admin.pages.order_detail', compact('order'));
    }
    public function sendMailInvoice(Request $request)
    {
        $id = $request->id;
        $order = Order::with('orderItems.product', 'shippingAddress', 'user', 'payment')->findOrFail($id);
        $userEmail = $order->user->email;
        try {
            Mail::send('admin.email.invoice', compact('order'), function ($message) use ($order) {
                $message->to($order->user->email);
                $message->subject('Hóa đơn đặt tour của khách hàng' . $order->shippingAddress->full_name);
            });
            return response()->json([
                'success' => true,
                'message' => 'Email đã được gửi thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra'
            ]);
        }
    }
    public function cancelOrder(Request $request)
    {
        $id = $request->id;
        $order = Order::findOrFail($id);
        if ($order) {
            foreach ($order->orderItems as $item) {
                //Update product stock
                $item->product->increment('stock', $item->quantity);
            }
            $order->status = 'canceled';
            $order->save();
            return response()->json([
                'success' => true,
                'message' => 'Hủy đơn hàng thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Đã có lỗi xảy ra'
        ]);
    }
}
