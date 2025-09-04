<?php

use App\Http\Controllers\ConfigController;
use Illuminate\Support\Facades\Route;

Route::get('/config', [ConfigController::class, 'getConfig']);
Route::post('/config', [ConfigController::class, 'update'])->middleware(['auth:sanctum', 'permission:dashboard']);