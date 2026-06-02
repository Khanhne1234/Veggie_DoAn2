<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Product $product)
    {
        $product->load(['reviews' => function ($query) {
            $query->latest();
        }, 'reviews.user']);

        return view('clients.components.include.review-list', compact('product'))->render();
    }


    //Hiển thị danh sách đánh giá cho một sản phẩm
    public function createReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = new Review();
        $review->user_id = Auth::id();
        $review->product_id = $request->input('product_id');
        $review->rating = $request->input('rating');
        $review->comment = $request->input('comment', ''); // Nếu có trường comment, nếu không thì để trống
        $review->save();

        return response()->json(['status' => 'success', 'message' => 'Đánh giá đã được thêm thành công!'], 201);
    }
}
