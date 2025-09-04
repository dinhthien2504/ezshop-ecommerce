<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OverviewController extends Controller
{
    public function getDashboardStats()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            // Thống kê cơ bản
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $activeProducts = Product::where('status', 1)->count();
            $totalShops = Shop::count();
            $activeShops = Shop::where('status', 1)->count();
            $totalCategories = Category::count();
            
            // Thống kê đơn hàng
            $totalOrders = Order::count();
            $pendingOrders = Order::where('order_status', 1)->count();
            $confirmedOrders = Order::where('order_status', 2)->count();
            
            // Thống kê doanh thu
            $totalRevenue = Order::whereIn('order_status', [2, 3, 4, 5])
                ->sum('total_amount');
            
            // Thống kê theo tháng hiện tại
            $currentMonth = Carbon::now()->format('Y-m');
            $monthlyOrders = Order::whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$currentMonth])
                ->count();
            $monthlyRevenue = Order::whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$currentMonth])
                ->whereIn('order_status', [2, 3, 4, 5])
                ->sum('total_amount');
            
            // Thống kê tăng trưởng so với tháng trước
            $lastMonth = Carbon::now()->subMonth()->format('Y-m');
            $lastMonthOrders = Order::whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$lastMonth])
                ->count();
            $lastMonthRevenue = Order::whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$lastMonth])
                ->whereIn('order_status', [2, 3, 4, 5])
                ->sum('total_amount');
            
            // Tính tỷ lệ tăng trưởng
            $orderGrowth = $lastMonthOrders > 0 
                ? (($monthlyOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
                : 0;
            $revenueGrowth = $lastMonthRevenue > 0 
                ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                : 0;
            
            return response()->json([
                'success' => true,
                'overview' => [
                    'total_users' => $totalUsers,
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'total_shops' => $totalShops,
                    'active_shops' => $activeShops,
                    'total_categories' => $totalCategories,
                    'total_orders' => $totalOrders,
                    'pending_orders' => $pendingOrders,
                    'confirmed_orders' => $confirmedOrders,
                    'total_revenue' => floatval($totalRevenue),
                    'monthly_orders' => $monthlyOrders,
                    'monthly_revenue' => floatval($monthlyRevenue),
                    'order_growth' => round($orderGrowth, 2),
                    'revenue_growth' => round($revenueGrowth, 2)
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dashboard Stats Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'message' => 'Lỗi khi lấy thống kê dashboard',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function getRevenueStats(Request $request)
    {
        try {
            $filter = $request->get('filter', 'month'); // Default: month
            $now = Carbon::now();
            
            // Xác định khoảng thời gian dựa trên filter
            switch ($filter) {
                case 'today':
                    $startDate = $now->copy()->startOfDay();
                    $endDate = $now->copy()->endOfDay();
                    $previousStartDate = $now->copy()->subDay()->startOfDay();
                    $previousEndDate = $now->copy()->subDay()->endOfDay();
                    break;
                    
                case 'week':
                    $startDate = $now->copy()->startOfWeek();
                    $endDate = $now->copy()->endOfWeek();
                    $previousStartDate = $now->copy()->subWeek()->startOfWeek();
                    $previousEndDate = $now->copy()->subWeek()->endOfWeek();
                    break;
                    
                case 'quarter':
                    $startDate = $now->copy()->firstOfQuarter();
                    $endDate = $now->copy()->lastOfQuarter();
                    $previousStartDate = $now->copy()->subQuarter()->firstOfQuarter();
                    $previousEndDate = $now->copy()->subQuarter()->lastOfQuarter();
                    break;
                    
                case 'year':
                    $startDate = $now->copy()->startOfYear();
                    $endDate = $now->copy()->endOfYear();
                    $previousStartDate = $now->copy()->subYear()->startOfYear();
                    $previousEndDate = $now->copy()->subYear()->endOfYear();
                    break;
                    
                case 'month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    $previousStartDate = $now->copy()->subMonth()->startOfMonth();
                    $previousEndDate = $now->copy()->subMonth()->endOfMonth();
                    break;
            }
            
            // Tính doanh thu kỳ hiện tại
            $currentRevenue = Order::whereBetween('created_date', [$startDate, $endDate])
                ->whereIn('order_status', [2, 3, 4, 5])
                ->sum('total_amount');
            
            // Tính doanh thu kỳ trước
            $previousRevenue = Order::whereBetween('created_date', [$previousStartDate, $previousEndDate])
                ->whereIn('order_status', [2, 3, 4, 5])
                ->sum('total_amount');
            
            // Tính tỷ lệ tăng trưởng
            $growth = $previousRevenue > 0 
                ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
                : 0;
            
            return response()->json([
                'success' => true,
                'revenue_stats' => [
                    'amount' => floatval($currentRevenue),
                    'growth' => round($growth, 2),
                    'filter' => $filter,
                    'period' => [
                        'start' => $startDate->format('Y-m-d'),
                        'end' => $endDate->format('Y-m-d')
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Revenue Stats Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi lấy thống kê doanh thu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRevenueChart(Request $request)
    {
        try {
            // Lấy 12 tháng gần nhất
            $chartData = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStr = $month->format('Y-m');
                
                $revenue = Order::whereRaw("DATE_FORMAT(created_date, '%Y-%m') = ?", [$monthStr])
                    ->whereIn('order_status', [2, 3, 4, 5])
                    ->sum('total_amount');
                
                $chartData[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => floatval($revenue)
                ];
            }
            
            return response()->json([
                'success' => true,
                'chart_data' => $chartData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Revenue Chart Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi lấy dữ liệu biểu đồ',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getCategoryStats()
    {
        try {
            $categoryStats = Category::select('tbl_categories.name as category_name', DB::raw('COUNT(tbl_products.id) as product_count'))
                ->leftJoin('tbl_products', 'tbl_categories.id', '=', 'tbl_products.category_id')
                ->groupBy('tbl_categories.id', 'tbl_categories.name')
                ->orderBy('product_count', 'desc')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'category_stats' => $categoryStats
            ]);
            
        } catch (\Exception $e) {
            Log::error('Category Stats Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi lấy thống kê danh mục',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getTopShops(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 5); // Default 5 items per page

            $topShops = Shop::select(
                'tbl_shops.id',
                'tbl_shops.shop_name',
                'tbl_shops.image as shop_avatar',
                'tbl_users.user_name as owner_name',
                DB::raw('COALESCE(SUM(tbl_orders.total_amount), 0) as total_revenue'),
                DB::raw('COUNT(tbl_orders.id) as total_orders')
            )
            ->leftJoin('tbl_users', 'tbl_shops.owner_id', '=', 'tbl_users.id')
            ->leftJoin('tbl_orders', function($join) {
                $join->on('tbl_shops.id', '=', 'tbl_orders.shop_id')
                     ->whereIn('tbl_orders.order_status', [2, 3, 4, 5]);
            })
            ->where('tbl_shops.status', 1)
            ->groupBy('tbl_shops.id', 'tbl_shops.shop_name', 'tbl_shops.image', 'tbl_users.user_name')
            ->orderBy('total_revenue', 'desc')
            ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'top_shops' => $topShops->items(),
                'current_page' => $topShops->currentPage(),
                'last_page' => $topShops->lastPage(),
                'per_page' => $topShops->perPage(),
                'total' => $topShops->total(),
                'meta' => [
                    'current_page' => $topShops->currentPage(),
                    'last_page' => $topShops->lastPage(),
                    'per_page' => $topShops->perPage(),
                    'total' => $topShops->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Top Shops Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi lấy top shops',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getRecentOrders(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 5); // Default 5 items per page

            $recentOrders = Order::select(
                'tbl_orders.id',
                'tbl_orders.total_amount',
                'tbl_orders.order_status',
                'tbl_orders.created_date',
                'tbl_users.user_name as customer_name',
                'tbl_shops.shop_name'
            )
            ->leftJoin('tbl_users', 'tbl_orders.user_id', '=', 'tbl_users.id')
            ->leftJoin('tbl_shops', 'tbl_orders.shop_id', '=', 'tbl_shops.id')
            ->orderBy('tbl_orders.created_date', 'desc')
            ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'recent_orders' => $recentOrders->items(),
                'current_page' => $recentOrders->currentPage(),
                'last_page' => $recentOrders->lastPage(),
                'per_page' => $recentOrders->perPage(),
                'total' => $recentOrders->total(),
                'meta' => [
                    'current_page' => $recentOrders->currentPage(),
                    'last_page' => $recentOrders->lastPage(),
                    'per_page' => $recentOrders->perPage(),
                    'total' => $recentOrders->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Recent Orders Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Lỗi khi lấy đơn hàng gần đây',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
