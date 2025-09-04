<?php

use App\Http\Controllers\LocationController;

Route::get('/provinces', [LocationController::class, 'provinces']);
Route::get('/districts/{province_id}', [LocationController::class, 'districts']);
Route::get('/wards/{district_id}', [LocationController::class, 'wards']);