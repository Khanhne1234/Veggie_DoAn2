<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        // Try to get in_stock products, fallback to all products
        // Keep sold-out products discoverable so customers can see their stock status.
        $query = Product::with('firstImage');

        if (request()->has('category_id') && !empty(request('category_id'))) {
            $query->where('category_id', request('category_id'));
        }

        $products = $query->paginate(9);

        $productsHightRate = Product::withAvg('reviews', 'rating')
            ->with(['reviews', 'firstImage'])
            ->orderBy('reviews_avg_rating', 'desc')
            ->limit(2)
            ->get();

        // Fallback if no in_stock products found
        if ($products->isEmpty() && $products->total() == 0) {
            $fallbackQuery = Product::with('firstImage');
            if (request()->has('category_id') && !empty(request('category_id'))) {
                $fallbackQuery->where('category_id', request('category_id'));
            }
            $products = $fallbackQuery->paginate(6);
        }
        return view('clients.pages.products', compact('categories', 'products', 'productsHightRate'));
    }
    public function filter(Request $request)
    {
        $query = Product::with('firstImage');

        // Optionally filter by status if needed
        // $query->where('status', 'in_stock');

        //Filter Category if exists
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
        }
        //Filter by price range if exists
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        //Filter sort by price if exists
        if ($request->has('sort_by')) {
            switch ($request->sort_by) {
                case 'price_low_high':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high_low':
                    $query->orderBy('price', 'desc');
                    break;
                case 'new_arrivals':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        }
        $products = $query->paginate(6); //hiển thị 6 sản phẩm mỗi trang
        return response()->json([
            'products' => view('clients.components.products_grid', compact('products'))->render(),
            'pagination' => (string) $products->links('clients.components.pagination.pagination_custom')
        ]);
    }
    public function detail($slug)
    {
        $product = Product::with('category', 'images', 'firstImage', 'reviews.user')->where('slug', $slug)->firstOrFail();

        $product->image_url = $product->firstImage?->image
            ? asset('storage/uploads/products/' . $product->firstImage->image)
            : asset('storage/uploads/products/default_product.webp');

        //Get product in the same category
        $relatedProducts = Product::with('firstImage')->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Exclude current product
            ->take(6)
            ->get();

        // Tính điểm đánh giá trung bình
        $averageRating = round($product->reviews()->avg('rating') ?? 0, 1);

        $hasPurchased = false;
        $hasReviewed = false;

        if (Auth::check()) {
            $user = Auth::user();

            $hasPurchased = OrderItem::whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['pending', 'completed']); // Chỉ tính các đơn hàng đang xử lý hoặc đã hoàn thành
            })->where('product_id', $product->id)->exists();

            $hasReviewed = Review::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();
        }
        foreach ($relatedProducts as $rel) {
            $rel->image_url = $rel->firstImage?->image
                ? asset('storage/uploads/products/' . $rel->firstImage->image) : asset('storage/uploads/products/default_product.webp');
        }
        return view('clients.pages.product_detail', compact('product', 'relatedProducts', 'hasPurchased', 'hasReviewed', 'averageRating'));
    }
}
