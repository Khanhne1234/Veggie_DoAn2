<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('default', 1)->first() ?? $addresses->first();

        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        return view('clients.pages.checkout', compact('addresses', 'defaultAddress', 'cartItems', 'totalPrice'));
    }

    public function getAddress(Request $request)
    {
        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())->first();
        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Địa chỉ không tồn tại'], 404);
        }
        return response()->json([
            'success' => true,
            'address' => $address
        ]);
    }
    // Đặt hàng với phương thức thanh toán tiền mặt
    public function placeOrder(Request $request)
    {
        if (!$request->filled('payment_method') || $request->input('payment_method') !== 'cash') {
            return redirect()->back()->withInput()->with('toastr', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn phương thức thanh toán.'
            ]);
        }

        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->withErrors(['error' => 'Giỏ hàng trống']);
        }
        DB::beginTransaction();

        try {
            // Tạo đơn hàng
            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->input('address_id');
            $order->total_price = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }) + 15000; // Cộng thêm phí vận chuyển
            $order->status = 'pending'; // Trạng thái ban đầu
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $product->name . ' không đủ số lượng trong kho.');
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            //Create payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->input('payment_method', 'cash'),
                'amount' => $order->total_price,
                'status' => 'pending',
                'paid_at' => null,
            ]);
            //Delete product in cart when order
            CartItem::where('user_id', $user->id)->delete();
            DB::commit();

            Notification::create([
                'user_id' => $user->id,
                'type' => 'order',
                'message' => 'Có đơn hàng mới từ ' . $user->email . ' vào lúc ' . now()->format('d/m/Y'),
                'link' => '/orders',
                'is_read' => false,
            ]);

            return redirect()->route('account')->with('toastr', [
                'type' => 'success',
                'message' => 'Đặt hàng thành công! Vui lòng kiểm tra email để biết chi tiết đơn hàng.'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('checkout')->withErrors(['error' => 'Đã có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.']);
        }
    }
    //Đặt hàng với phương thức thanh toán Paypal
    public function placeOrderPaypal(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $cartItems = CartItem::where('user_id', $user->id)->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Giỏ hàng trống.'], 422);
            }

            $totalPrice = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }) + 15000;
            // Tạo đơn hàng
            $order = new Order();
            $order->user_id = $user->id;
            $order->shipping_address_id = $request->input('address_id');
            $order->total_price = $totalPrice;
            $order->status = 'pending'; // Trạng thái ban đầu
            $order->save();

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);
                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new \Exception('Sản phẩm ' . $product->name . ' không đủ số lượng trong kho.');
                }
                $product->stock -= $item->quantity;
                $product->save();
            }

            //Create payment
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => 'paypal',
                'transaction_id' => $request->transactionID,
                'amount' => $order->total_price,
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            //Delete product in cart when order
            CartItem::where('user_id', $user->id)->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Đặt hàng thành công! Vui lòng kiểm tra email để biết chi tiết đơn hàng.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Đã có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.']);
        }
    }
}
