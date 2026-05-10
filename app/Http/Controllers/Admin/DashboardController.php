<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Overview stats
        $totalRevenue    = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders     = Order::count();
        $totalCustomers  = User::whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'super-admin']);
        })->count();
        $totalProducts   = Product::count();

        // Revenue this month vs last month
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $revenueLastMonth = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');

        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 100;

        // Orders this month
        $ordersThisMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Daily revenue for last 30 days (for chart)
        $dailyRevenue = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fill missing days with 0
        $last30Days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $day  = $dailyRevenue->firstWhere('date', $date);
            $last30Days->push([
                'date'    => now()->subDays($i)->format('M d'),
                'revenue' => $day ? round($day->revenue, 2) : 0,
                'orders'  => $day ? $day->orders : 0,
            ]);
        }

        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top selling products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.base_price',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name', 'products.base_price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(8)
            ->get();

        // Monthly revenue for last 6 months (for bar chart)
        $monthlyRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');
            $monthlyRevenue->push([
                'month'   => $month->format('M Y'),
                'revenue' => round($revenue, 2),
            ]);
        }

        // Payment method breakdown
        $paymentMethods = Order::where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalCustomers', 'totalProducts',
            'revenueThisMonth', 'revenueLastMonth', 'revenueGrowth',
            'ordersThisMonth', 'last30Days', 'ordersByStatus',
            'topProducts', 'recentOrders', 'monthlyRevenue', 'paymentMethods'
        ));
    }
}