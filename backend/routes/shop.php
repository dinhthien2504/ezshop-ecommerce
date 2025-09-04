<?php

use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShopFollowerController;
use Illuminate\Support\Facades\Route;

Route::post('shops', [ShopController::class, 'store'])
    ->middleware('auth:sanctum');

Route::get('shops/bank-account', [ShopController::class, 'getBankAccountInfo'])
    ->middleware('auth:sanctum');

Route::put('shops/bank-account', [ShopController::class, 'saveBankAccountInfo'])
    ->middleware('auth:sanctum');

Route::get('shops/calculate-balance', [ShopController::class, 'getBalance'])
    ->middleware('auth:sanctum');

Route::get('shops/statistics/overview', [ShopController::class, 'getStatistics'])
    ->middleware('auth:sanctum');

Route::get('shops/analytics', [ShopController::class, 'getAnalytics'])
    ->middleware(['auth:sanctum']);

Route::get('shops/chart-analytics', [ShopController::class, 'getChartAnalytics'])
    ->middleware(['auth:sanctum']);

Route::post('/shop/update', [ShopController::class, 'updateShop'])
    ->middleware('auth:sanctum');

Route::get('/get-shop', [ShopController::class, 'get_shop'])
    ->middleware('auth:sanctum');

// ===== ADMIN SHOP MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:shop'])->group(function () {
    Route::get('shops', [ShopController::class, 'getShops']);
    Route::post('shop/change-status/{id}', [ShopController::class, 'changeStatus'])
        ->where('id', '[0-9]+');
    Route::post('shop/change-multiple', [ShopController::class, 'changeMultiple']);
});

// ===== SHOP OWNER APIs =====

Route::get('shops/{id}/shop-info', [ShopController::class, 'getShopById'])
    ->where('id', '[0-9]+');

Route::post('shop/check-follow', [ShopFollowerController::class, 'checkFollow'])
    ->middleware('auth:sanctum');

Route::post('shop/follow', [ShopFollowerController::class, 'followShop'])
    ->middleware('auth:sanctum');

Route::get('shop/following', [ShopFollowerController::class, 'getFollowingShops'])
    ->middleware('auth:sanctum');
