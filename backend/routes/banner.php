<?php

use App\Http\Controllers\BannerController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN BANNER MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:setting'])->group(function () {
    Route::get('/banner', [BannerController::class, 'getBanners']);
    Route::post('/banner', [BannerController::class, 'store']);
    Route::put('/banner/{id}', [BannerController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/banner/{id}', [BannerController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/banner/delete-multiple', [BannerController::class, 'deleteMultiple']);
});

// ===== PUBLIC BANNER APIs =====
Route::get('/home-banner', [BannerController::class, 'getHomeBanners']);
