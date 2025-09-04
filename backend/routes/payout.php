<?php

use App\Http\Controllers\PayoutController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN PAYOUT MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:finance'])->group(function () {
    Route::get('payouts', [PayoutController::class, 'index']);
    Route::post('payouts/{id}/process', [PayoutController::class, 'processPayout']);
    Route::post('payouts/{id}/reject', [PayoutController::class, 'rejectPayout']);
    Route::post('payouts/{id}/retry', [PayoutController::class, 'retryPayout']);
});

// ===== SHOP PAYOUT APIs =====
Route::get('payouts/shop', [PayoutController::class, 'getPayoutsByShop'])
    ->middleware('auth:sanctum');

Route::post('payouts', [PayoutController::class, 'store'])
    ->middleware('auth:sanctum');