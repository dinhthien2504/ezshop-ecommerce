<?php

use App\Http\Controllers\RefundController;
use Illuminate\Support\Facades\Route;
Route::post('refunds', [RefundController::class, 'store'])
    ->middleware(['auth:sanctum']);
Route::put('refunds/{id}', [RefundController::class, 'update'])
    ->middleware(['auth:sanctum']);
