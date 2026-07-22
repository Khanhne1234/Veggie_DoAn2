<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $users = User::where('role_id', 3)->latest()->get();
        $categories = Category::with('products')->get();
        $products = Product::where('stock', '>', 0)->latest()->get();
        $orders = Order::with('shippingAddress')->latest()->limit(5)->get();

        //Get list top3 bestseller product

        $topSellingProducts = Product::withSum('oderItems as total_sold', 'quantity')
            ->orderBy('total_sold', 'desc')
            ->limit(3)
            ->get();

        $monthlyRevenue = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        return view('admin.pages.dashboard', compact('users', 'categories', 'products', 'orders', 'topSellingProducts', 'monthlyRevenue'));
    }
}
