<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->get();

        return view('clients.pages.wishlist', compact('wishlist'));
    }

    public function addToWishlist(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập trước khi thêm vào wishlist.',
            ], 401);
        }

        $user_id = Auth::id();
        $productId = $request->input('product_id');

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $user_id,
            'product_id' => $productId,
        ]);

        $message = $wishlist->wasRecentlyCreated
            ? 'Sản phẩm đã được thêm vào wishlist.'
            : 'Sản phẩm đã có trong wishlist.';

        return response()->json([
            'status' => true,
            'message' => $message,
        ]);
    }

    public function removeFromWishlist(Request $request)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->input('product_id'))
            ->delete();
        return response()->json([
            'status' => true,
            'message' => 'Sản phẩm đã được xóa khỏi wishlist.',
        ]);
    }
}
