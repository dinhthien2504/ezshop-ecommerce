<?php

use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN PLATFORM VOUCHER MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:voucher'])->group(function () {
    Route::get('/vouchers/platform', [VoucherController::class, 'getVouchersPlatform'])
        ->name('vouchers.platform');
    Route::post('/vouchers', [VoucherController::class, 'store'])
        ->name('vouchers.store');
    Route::put('/vouchers/{id}', [VoucherController::class, 'update'])
        ->name('vouchers.update');
    Route::patch('/vouchers/{id}', [VoucherController::class, 'unactive'])
        ->name('vouchers.unactive');
});

// ===== SHOP VOUCHER MANAGEMENT APIs =====
Route::get('/vouchers/shop', [VoucherController::class, 'getVouchersByShop'])
    ->middleware('auth:sanctum')
    ->name('vouchers.shop');

Route::post('/vouchers/shop', [VoucherController::class, 'store'])
    ->middleware('auth:sanctum')
    ->name('vouchers.store_shop');
Route::put('/vouchers/{id}/shop', [VoucherController::class, 'update'])
    ->name('vouchers.update_shop');
// ===== USER VOUCHER APIs =====
Route::get('/vouchers/active', [VoucherController::class, 'getAvailableVouchersForUser'])
    ->middleware('auth:sanctum')
    ->name('vouchers.active');