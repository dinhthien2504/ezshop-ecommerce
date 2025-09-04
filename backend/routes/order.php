<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/orders/user', [OrderController::class, 'orderByUser'])
    ->middleware('auth:sanctum');

Route::get('/orders/{id}/user', [OrderController::class, 'orderByUserOrderId'])
    ->middleware('auth:sanctum');

Route::post('/orders/{id}/update-status', [OrderController::class, 'changeStatus'])
    ->middleware('auth:sanctum');

Route::get('/orders/shop', [OrderController::class, 'getShopOrders'])
    ->middleware('auth:sanctum');

//ADMIN

Route::get('orders', [OrderController::class, 'index'])
    ->middleware('auth:sanctum', 'permission:order');
