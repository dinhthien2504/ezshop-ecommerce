<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallets')->group(function () {
    Route::get('', [WalletController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/user', [WalletController::class, 'show']);
});