<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private function getCartItems(): array
    {
        if (Auth::check()) {
            return CartItem::with('product.firstImage')
                ->where('user_id', Auth::id())
                ->get()
                ->map(function ($item) {
                    $product = $item->product;

                    return [
                        'product' => $product,
                        'quantity' => (int) $item->quantity,
                        'subtotal' => $product ? $product->price * $item->quantity : 0,
                    ];
                })
                ->filter(fn($item) => $item['product'] !== null)
                ->values()
                ->all();
        }

        return collect(session()->get('cart', []))
            ->map(function (array $item) {
                $product = Product::with('firstImage')->find($item['product_id'] ?? null);

                return [
                    'product' => $product,
                    'quantity' => (int) ($item['quantity'] ?? 0),
                    'subtotal' => $product ? $product->price * (int) ($item['quantity'] ?? 0) : 0,
                ];
            })
            ->filter(fn($item) => $item['product'] !== null)
            ->values()
            ->all();
    }

    public function addToCart(Request $request)
    {
        Log::info('addToCart called', ['user_id' => Auth::id(), 'payload' => $request->all()]);
        $request->merge(['quantity' => (int)$request->quantity]);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        // Kiểm tra nếu số lượng yêu cầu vượt quá tồn kho
        if ($request->quantity > $product->stock) {
            return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
        }
        // Nếu người dùng đã đăng nhập, thêm sản phẩm vào giỏ hàng của họ
        if (Auth::check()) {
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $request->product_id) // Tìm kiếm sản phẩm trong giỏ hàng của người dùng
                ->first(); // Kiểm tra nếu sản phẩm đã tồn tại trong giỏ hàng
            if ($cartItem) {
                $cartItem->quantity += $request->quantity; // Cộng dồn số lượng nếu sản phẩm đã tồn tại trong giỏ hàng
                $cartItem->save(); // Cập nhật số lượng nếu sản phẩm đã tồn tại trong giỏ hàng
                Log::info('cartItem updated', ['id' => $cartItem->id, 'quantity' => $cartItem->quantity]);
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity,
                ]);
                Log::info('cartItem created', ['user_id' => Auth::id(), 'product_id' => $request->product_id, 'quantity' => $request->quantity]);
            }
            $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity'); // Tổng số lượng sản phẩm trong giỏ hàng của người dùng
            Log::info('cartCount for user', ['user_id' => Auth::id(), 'cart_count' => $cartCount]);
        } else {
            // Nếu người dùng chưa đăng nhập, lưu sản phẩm vào session
            $cart = session()->get('cart', []);
            if (isset($cart[$request->product_id])) {
                $cart[$request->product_id]['quantity'] += $request->quantity; // Cộng dồn số lượng nếu sản phẩm đã tồn tại trong giỏ hàng
            } else {
                $cart[$request->product_id] = [
                    'product_id' => $request->product_id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $request->quantity,
                    'stock' => $product->stock,
                    'image' => $product->firstImage?->image ?? 'upload/products/default_product.webp',
                ];
            }
            session()->put('cart', $cart);
            Log::info('cart saved to session', ['session_cart' => $cart]);
            $cartCount = array_sum(array_column($cart, 'quantity')); // Tổng số lượng sản phẩm trong giỏ hàng của người dùng
        }
        return response()->json([
            'success' => true,
            'message' => 'Thêm vào giỏ hàng thành công',
            'cart_count' => $cartCount
        ]);
    }
    // Load mini cart
    public function loadMiniCart()
    {
        $cartItems = $this->getCartItems();
        return response()->json([
            'status' => true,
            'html' => view('clients.components.include.mini_cart', compact('cartItems'))->render()
        ]);
    }
    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id',]);

        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->delete();
            $cartCount = CartItem::where('user_id', Auth::id())->sum('quantity'); // Tổng số lượng sản phẩm trong giỏ hàng của người dùng
        } else {
            // Nếu người dùng chưa đăng nhập, xóa sản phẩm khỏi session
            $cart = session()->get('cart', []);
            unset($cart[$request->product_id]);
            session()->put('cart', $cart);
            $cartCount = array_sum(array_column($cart, 'quantity')); // Tổng số lượng sản phẩm trong giỏ hàng của người dùng
        }

        return response()->json([
            'status' => true,
            'cart_count' => $cartCount,
            'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng'
        ]);
    }
    // Hiển thị trang giỏ hàng
    public function showCart()
    {
        $cartItems = $this->getCartItems();

        return view('clients.pages.cart', compact('cartItems'));
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity');
        if (Auth::check()) {
            $cartItems = CartItem::where('user_id', Auth::id())->where('product_id', $productId)->first();
            if (!$cartItems) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $product = Product::find($productId);

            if ($quantity > $product->stock) {
                return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
            }
            $cartItems->quantity = $quantity;
            $cartItems->save();
        } else {
            $cart = session()->get('cart', []);
            if (!isset($cart[$productId])) {
                return response()->json(['error' => 'Sản phẩm không tồn tại trong giỏ hàng'], 404);
            }

            $product = Product::find($productId);
            if ($quantity > $product->stock) {
                return response()->json(['error' => 'Số lượng vượt quá tồn kho'], 400);
            }

            $cart[$productId]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        $cartCount = Auth::check()
            ? CartItem::where('user_id', Auth::id())->sum('quantity')
            : array_sum(array_column(session()->get('cart', []), 'quantity'));
        // Tính lại tổng số lượng sản phẩm trong giỏ hàng
        $subTotal = $product->price * $quantity;
        $total = $this->calculateTotalCart();
        $grandTotal = $total + 15000; // Nếu có phí vận chuyển hoặc thuế, bạn có thể cộng thêm vào đây
        return response()->json([
            'quantity' => $quantity,
            'subTotal' => number_format($subTotal, 0, ',', '.'),
            'total' => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($grandTotal, 0, ',', '.'),
            'cart_count' => $cartCount,
        ]);
    }
    private function calculateTotalCart(): float
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->with('product')->get()->sum(fn($item) => $item->product ? $item->product->price * $item->quantity : 0);
        } else {
            $cartItems = session()->get('cart', []);
            return collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        }
    }

    //Hanle xóa sản phẩm khỏi giỏ hàng
    public function removeCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity');
        if (Auth::check()) {
            CartItem::where('user_id', Auth::id())->where('product_id', $productId)->delete();
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }
        // Cập nhật lại số lượng sản phẩm trong giỏ hàng sau khi xóa
        $cartCount = Auth::check()
            ? CartItem::where('user_id', Auth::id())->sum('quantity')
            : array_sum(array_column(session()->get('cart', []), 'quantity'));
        // Tính lại tổng số lượng sản phẩm trong giỏ hàng
        $total = $this->calculateTotalCart();
        $grandTotal = $total + 15000; // Nếu có phí vận chuyển hoặc thuế, bạn có thể cộng thêm vào đây
        return response()->json([
            'total' => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($grandTotal, 0, ',', '.'),
            'cart_count' => $cartCount,
        ]);
    }
}
