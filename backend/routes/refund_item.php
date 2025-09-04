<?php

use App\Http\Controllers\RefundItemController;
use Illuminate\Support\Facades\Route;

Route::post('refund_items/calculate', [RefundItemController::class, 'calculateRefundItemAmount'])
    ->middleware(['auth:sanctum']);