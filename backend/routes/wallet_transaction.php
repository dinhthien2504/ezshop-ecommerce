<?php

use App\Http\Controllers\WalletTransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('wallet-transactions')->group(function () {
    Route::get('history', [WalletTransactionController::class, 'history']);
});