<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::post('/notifications', [NotificationController::class, 'create'])->middleware('auth:sanctum');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->middleware('auth:sanctum');
Route::post('/notifications/read/{id}', [NotificationController::class, 'markAsRead'])->middleware('auth:sanctum');
Route::get('/notifications/user', [NotificationController::class, 'getUserNotifications'])->middleware('auth:sanctum');
Route::get('/notifications/shop', [NotificationController::class, 'getShopNotifications'])->middleware('auth:sanctum');
Route::get('/notifications/admin', [NotificationController::class, 'getAdminNotifications'])->middleware('auth:sanctum');