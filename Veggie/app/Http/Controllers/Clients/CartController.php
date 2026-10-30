<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Services\CartInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private function getCartItems(): array
    {
        if (Auth::check()) {
            return CartItem::with('product.firstImage')
                ->where('user_id', Auth::id())
                ->get()
                ->groupBy('product_id')
                ->map(function ($items) {
                    $product = $items->first()->product;
                    $quantity = (int) $items->sum('quantity');
                    $invalid = $items->first(fn ($item) => (int) $item->quantity < 1);

                    return $this->cartRow($product, $quantity, $invalid ? (int) $invalid->quantity : null);
                })
                ->filter(fn($item) => $item['product'] !== null)
                ->values()
                ->all();
        }

        $cart = session()->get('cart', []);
        $products = Product::with('firstImage')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        return collect($cart)
            ->map(fn (array $item, $id) => $this->cartRow($products->get($id), (int) ($item['quantity'] ?? 0)))
            ->filter(fn($item) => $item['product'] !== null)
            ->values()
            ->all();
    }

    private function cartRow(?Product $product, int $quantity, ?int $invalidQuantity = null): array
    {
        return [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $product ? $product->price * $quantity : 0,
            'stock' => max(0, (int) ($product?->stock ?? 0)),
            'is_out_of_stock' => !$product || $product->stock <= 0,
            'stock_issue' => CartInventory::issue($product, $invalidQuantity ?? $quantity),
        ];
    }

    private function viewData(): array
    {
        $cartItems = $this->getCartItems();
        $cartTotal = collect($cartItems)->sum('subtotal');
        $hasStockIssues = collect($cartItems)->contains(fn ($item) => $item['stock_issue'] !== null);
        $canCheckout = count($cartItems) > 0 && !$hasStockIssues;

        return compact('cartItems', 'cartTotal', 'hasStockIssues', 'canCheckout');
    }

    private function cartResponse(): array
    {
        $data = $this->viewData();
        $total = $data['cartTotal'];

        return [
            'cart_count' => (int) collect($data['cartItems'])->sum('quantity'),
            'total' => number_format($total, 0, ',', '.'),
            'grandTotal' => number_format($total + (count($data['cartItems']) ? 15000 : 0), 0, ',', '.'),
            'can_checkout' => $data['canCheckout'],
            'has_stock_issues' => $data['hasStockIssues'],
            'cart_html' => view('clients.components.include.cart_content', $data)->render(),
            'mini_cart_html' => view('clients.components.include.mini_cart_content', $data)->render(),
        ];
    }

    public function addToCart(Request $request)
    {
        return $this->changeQuantity($request, true);
    }
    // Load mini cart
    public function loadMiniCart()
    {
        $state = $this->cartResponse();

        return response()->json(['status' => true, 'html' => $state['mini_cart_html']] + $state);
    }
    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart(Request $request)
    {
        return $this->removeCart($request);
    }
    // Hiển thị trang giỏ hàng
    public function showCart()
    {
        return view('clients.pages.cart', $this->viewData());
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    public function updateCart(Request $request)
    {
        return $this->changeQuantity($request, false);
    }

    private function changeQuantity(Request $request, bool $adding)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'product_id.required' => 'Không tìm thấy sản phẩm.',
            'product_id.integer' => 'Sản phẩm không hợp lệ.',
            'product_id.exists' => 'Sản phẩm không còn tồn tại.',
            'quantity.required' => 'Vui lòng chọn số lượng.',
            'quantity.integer' => 'Số lượng phải là số nguyên lớn hơn 0.',
            'quantity.min' => 'Số lượng phải là số nguyên lớn hơn 0.',
        ]);

        if ($validator->fails()) {
            $productId = filter_var($request->input('product_id'), FILTER_VALIDATE_INT);
            $product = $productId ? Product::find($productId) : null;
            $current = Auth::check()
                ? (int) CartItem::where('user_id', Auth::id())->where('product_id', $productId ?: 0)->sum('quantity')
                : (int) (session('cart', [])[$productId ?: 0]['quantity'] ?? 0);
            $message = $validator->errors()->first();

            $response = [
                'success' => false,
                'error' => $message,
                'message' => $message,
                'code' => $validator->errors()->has('quantity') ? 'invalid_quantity' : 'product_unavailable',
                'stock' => max(0, (int) ($product?->stock ?? 0)),
                'cart_quantity' => $current,
                'errors' => $validator->errors(),
            ];
            if ($product) {
                $issue = CartInventory::issue($product, 1, $product->id);
                if ($issue) {
                    $response['stock'] = $issue['stock'];
                }
            }

            return response()->json($response + $this->cartResponse(), 422);
        }

        $productId = (int) $request->input('product_id');
        $requested = (int) $request->input('quantity');
        $result = DB::transaction(function () use ($productId, $requested, $adding) {
            if (Auth::check()) {
                User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            }
            $product = Product::whereKey($productId)->lockForUpdate()->first();
            $rows = Auth::check()
                ? CartItem::where('user_id', Auth::id())->where('product_id', $productId)->orderBy('id')->lockForUpdate()->get()
                : collect();
            $cart = session('cart', []);
            $current = Auth::check() ? (int) $rows->sum('quantity') : (int) ($cart[$productId]['quantity'] ?? 0);

            if (!$adding && (Auth::check() ? $rows->isEmpty() : !isset($cart[$productId]))) {
                return ['error' => 'Sản phẩm không tồn tại trong giỏ hàng.', 'message' => 'Sản phẩm không tồn tại trong giỏ hàng.', 'code' => 'cart_item_missing', 'status_code' => 404];
            }

            $quantity = $adding ? $current + $requested : $requested;
            $issue = CartInventory::issue($product, $quantity, $productId);
            if ($issue) {
                $issue['cart_quantity'] = $current;
                if ($adding && $current > 0 && $issue['stock'] > 0) {
                    $issue['message'] .= " Giỏ hàng của bạn đã có {$current} {$issue['unit']} sản phẩm này.";
                }

                return $issue + ['error' => $issue['message'], 'status_code' => 422];
            }

            if (Auth::check()) {
                if ($rows->isNotEmpty()) {
                    $rows->first()->update(['quantity' => $quantity]);
                    CartItem::whereIn('id', $rows->skip(1)->pluck('id'))->delete();
                } else {
                    CartItem::create(['user_id' => Auth::id(), 'product_id' => $productId, 'quantity' => $quantity]);
                }
            } else {
                $cart[$productId] = [
                    'product_id' => $productId,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'stock' => $product->stock,
                    'image' => $product->firstImage?->image ?? 'default_product.webp',
                ];
                session()->put('cart', $cart);
            }

            return [
                'success' => true,
                'message' => $adding ? 'Thêm vào giỏ hàng thành công' : 'Cập nhật giỏ hàng thành công',
                'quantity' => $quantity,
                'subTotal' => number_format($product->price * $quantity, 0, ',', '.'),
            ];
        });

        $status = $result['status_code'] ?? 200;
        unset($result['status_code']);

        return response()->json($result + ['success' => $status === 200] + $this->cartResponse(), $status);
    }
    //Hanle xóa sản phẩm khỏi giỏ hàng
    public function removeCart(Request $request)
    {
        $request->validate(['product_id' => 'required|integer|min:1']);
        $productId = (int) $request->input('product_id');
        if (Auth::check()) {
            DB::transaction(function () use ($productId) {
                User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
                CartItem::where('user_id', Auth::id())->where('product_id', $productId)->delete();
            });
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'status' => true,
            'success' => true,
            'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng',
        ] + $this->cartResponse());
    }
}
