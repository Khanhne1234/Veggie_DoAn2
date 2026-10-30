<?php

namespace App\Http\Controllers\Clients;

use App\Exceptions\CartInventoryException;
use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\User;
use App\Services\CartInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $cartItems = CartItem::where('user_id', $user->id)->with('product')->get();
        $issues = $this->inventoryIssues($cartItems);

        if ($issues !== []) {
            return $this->redirectToCart($issues);
        }

        $addresses = ShippingAddress::where('user_id', $user->id)->get();
        $defaultAddress = $addresses->where('default', 1)->first() ?? $addresses->first();
        $totalPrice = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('clients.pages.checkout', compact('addresses', 'defaultAddress', 'cartItems', 'totalPrice'));
    }

    public function validateStock()
    {
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();

        return $this->stockResponse($this->inventoryIssues($cartItems));
    }

    public function getAddress(Request $request)
    {
        $address = ShippingAddress::where('id', $request->address_id)
            ->where('user_id', Auth::id())->first();

        if (!$address) {
            return response()->json(['success' => false, 'message' => 'Địa chỉ không tồn tại'], 404);
        }

        return response()->json(['success' => true, 'address' => $address]);
    }

    public function placeOrder(Request $request)
    {
        if ($request->input('payment_method') !== 'cash') {
            return redirect()->back()->withInput()->with('toastr', [
                'type' => 'warning',
                'message' => 'Vui lòng chọn phương thức thanh toán.',
            ]);
        }

        try {
            $this->createOrder($request, 'cash');

            return redirect()->route('account')->with('toastr', [
                'type' => 'success',
                'message' => 'Đặt hàng thành công! Vui lòng kiểm tra email để biết chi tiết đơn hàng.',
            ]);
        } catch (CartInventoryException $exception) {
            return $this->redirectToCart($exception->issues);
        } catch (\Throwable $exception) {
            report($exception);

            return redirect()->route('checkout')->with('toastr', [
                'type' => 'error',
                'message' => 'Đã có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.',
            ]);
        }
    }

    public function placeOrderPaypal(Request $request)
    {
        try {
            $this->createOrder($request, 'paypal');

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công! Vui lòng kiểm tra email để biết chi tiết đơn hàng.',
            ]);
        } catch (CartInventoryException $exception) {
            return $this->stockResponse($exception->issues);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.',
            ], 500);
        }
    }

    private function createOrder(Request $request, string $paymentMethod): Order
    {
        return DB::transaction(function () use ($request, $paymentMethod) {
            // Serialize checkout and cart writes for the same customer.
            $user = User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $cartItems = CartItem::where('user_id', $user->id)
                ->orderBy('product_id')->orderBy('id')->lockForUpdate()->get();
            $products = Product::whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            foreach ($cartItems as $item) {
                $item->setRelation('product', $products->get($item->product_id));
            }

            // Validate every line against locked, current stock before any writes.
            $issues = $this->inventoryIssues($cartItems);
            if ($issues !== []) {
                throw new CartInventoryException($issues);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $request->input('address_id'),
                'total_price' => $cartItems->sum(fn ($item) => $item->quantity * $item->product->price) + 15000,
                'status' => 'pending',
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->stock -= $item->quantity;
                $item->product->save();
            }

            $payment = [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $order->total_price,
                'status' => $paymentMethod === 'paypal' ? 'completed' : 'pending',
                'paid_at' => $paymentMethod === 'paypal' ? now() : null,
            ];

            if ($paymentMethod === 'paypal') {
                $payment['transaction_id'] = $request->input('transactionID');
            }

            Payment::create($payment);
            CartItem::where('user_id', $user->id)->whereIn('id', $cartItems->pluck('id'))->delete();

            if ($paymentMethod === 'cash') {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'order',
                    'message' => 'Có đơn hàng mới từ ' . $user->email . ' vào lúc ' . now()->format('d/m/Y'),
                    'link' => '/orders',
                    'is_read' => false,
                ]);
            }

            return $order;
        });
    }

    private function inventoryIssues(Collection $cartItems): array
    {
        if ($cartItems->isEmpty()) {
            return [[
                'code' => 'empty_cart',
                'product_id' => null,
                'name' => '',
                'unit' => '',
                'stock' => 0,
                'cart_quantity' => 0,
                'message' => 'Giỏ hàng của bạn đang trống.',
            ]];
        }

        return CartInventory::issues($cartItems);
    }

    private function redirectToCart(array $issues)
    {
        return redirect()->route('cart')->with('toastr', [
            'type' => 'warning',
            'message' => implode(' ', array_column($issues, 'message')),
        ]);
    }

    private function stockResponse(array $issues)
    {
        $valid = $issues === [];

        return response()->json([
            'success' => $valid,
            'valid' => $valid,
            'message' => $valid ? 'Giỏ hàng đủ tồn kho.' : implode(' ', array_column($issues, 'message')),
            'issues' => $issues,
            'redirect_url' => $valid ? null : route('cart'),
        ], $valid ? 200 : 422);
    }
}
