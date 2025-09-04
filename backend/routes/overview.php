<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OverviewController;

// ===== ADMIN DASHBOARD APIs =====
Route::prefix('admin')->middleware(['auth:sanctum', 'permission:dashboard'])->group(function () {
    Route::get('/dashboard/stats', [OverviewController::class, 'getDashboardStats']);
    Route::get('/dashboard/revenue-stats', [OverviewController::class, 'getRevenueStats']);
    Route::get('/dashboard/revenue-chart', [OverviewController::class, 'getRevenueChart']);
    Route::get('/dashboard/category-stats', [OverviewController::class, 'getCategoryStats']);
    Route::get('/dashboard/top-shops', [OverviewController::class, 'getTopShops']);
    Route::get('/dashboard/recent-orders', [OverviewController::class, 'getRecentOrders']);
});
