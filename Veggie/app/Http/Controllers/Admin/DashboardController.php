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
        $totalUsers = User::where('role_id', 3)->count();
        $totalProducts = Product::where('stock', '>', 0)->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total_price');

        $users = User::where('role_id', 3)->latest()->limit(5)->get();
        $categories = Category::withCount('products')
            ->orderByDesc('products_count')
            ->limit(5)
            ->get();
        $orders = Order::with('shippingAddress')->latest()->limit(5)->get();

        // Get the five best-selling products to match the dashboard table height.
        $topSellingProducts = Product::withSum('oderItems as total_sold', 'quantity')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $monthlyRevenue = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('SUM(total_price) as total_revenue')
        )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();

        return view('admin.pages.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'users',
            'categories',
            'orders',
            'topSellingProducts',
            'monthlyRevenue'
        ));
    }
}
