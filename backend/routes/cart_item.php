<?php

use App\Http\Controllers\CartItemController;
use GuzzleHttp\Middleware;
Route::get('/carts/user', [CartItemController::class, 'getUserCart'])
    ->middleware('auth:sanctum');

Route::get('/carts/user-header', [CartItemController::class, 'getUserCartForHeader'])
    ->middleware('auth:sanctum');

Route::post('/carts', [CartItemController::class, 'store'])
    ->middleware('auth:sanctum');

Route::put('/carts/{id}', [CartItemController::class, 'update'])
    ->middleware('auth:sanctum');

Route::delete('/carts', [CartItemController::class, 'destroy'])
    ->middleware('auth:sanctum');
