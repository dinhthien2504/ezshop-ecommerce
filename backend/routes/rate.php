<?php

use App\Http\Controllers\RateController;
use Illuminate\Support\Facades\Route;

Route::post('/rates', [RateController::class, 'store'])
    ->middleware('auth:sanctum');

Route::get('/rates/{id}', [RateController::class, 'getRatesByProductId']);