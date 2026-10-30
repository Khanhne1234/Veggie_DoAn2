<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::where('role_id', 3)->count();
        $totalProducts = Product::where('stock', '>', 0)->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereNotIn('status', ['canceled', 'cancelled'])->sum('total_price');

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

        $revenuePeriod = $request->query('revenue_period', 'month');
        if (!in_array($revenuePeriod, ['day', 'week', 'month'], true)) {
            $revenuePeriod = 'month';
        }

        $now = CarbonImmutable::now();
        $today = $now->startOfDay();
        $revenueLabels = [];
        $revenueTotals = [];

        if ($revenuePeriod === 'day') {
            $rangeStart = $today->subDays(29);
            $cursor = $rangeStart;
            while ($cursor->lessThanOrEqualTo($today)) {
                $key = $cursor->toDateString();
                $revenueLabels[$key] = $cursor->format('d/m');
                $revenueTotals[$key] = 0;
                $cursor = $cursor->addDay();
            }
            $revenuePeriodLabel = 'theo ngày';
        } elseif ($revenuePeriod === 'week') {
            $currentWeek = $today->startOfWeek(Carbon::MONDAY);
            $rangeStart = $currentWeek->subWeeks(11);
            $cursor = $rangeStart;
            while ($cursor->lessThanOrEqualTo($currentWeek)) {
                $key = $cursor->toDateString();
                $revenueLabels[$key] = $cursor->format('d/m') . ' – ' . $cursor->addDays(6)->format('d/m');
                $revenueTotals[$key] = 0;
                $cursor = $cursor->addWeek();
            }
            $revenuePeriodLabel = 'theo tuần';
        } else {
            $currentMonth = $today->startOfMonth();
            $rangeStart = $currentMonth->subMonths(11);
            $cursor = $rangeStart;
            while ($cursor->lessThanOrEqualTo($currentMonth)) {
                $key = $cursor->format('Y-m');
                $revenueLabels[$key] = 'T' . $cursor->format('m/Y');
                $revenueTotals[$key] = 0;
                $cursor = $cursor->addMonth();
            }
            $revenuePeriodLabel = 'theo tháng';
        }

        $revenueOrders = Order::query()
            ->whereNotIn('status', ['canceled', 'cancelled'])
            ->where('created_at', '>=', $rangeStart)
            ->where('created_at', '<=', $now)
            ->get(['created_at', 'total_price']);

        foreach ($revenueOrders as $order) {
            $createdAt = CarbonImmutable::instance($order->created_at);
            $key = match ($revenuePeriod) {
                'day' => $createdAt->toDateString(),
                'week' => $createdAt->startOfWeek(Carbon::MONDAY)->toDateString(),
                default => $createdAt->format('Y-m'),
            };

            if (array_key_exists($key, $revenueTotals)) {
                $revenueTotals[$key] += (float) $order->total_price;
            }
        }

        $revenueSeries = [
            'labels' => array_values($revenueLabels),
            'values' => array_values($revenueTotals),
        ];

        return view('admin.pages.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalRevenue',
            'users',
            'categories',
            'orders',
            'topSellingProducts',
            'revenueSeries',
            'revenuePeriod',
            'revenuePeriodLabel'
        ));
    }
}
