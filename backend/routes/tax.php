<?php

use App\Http\Controllers\TaxController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN TAX MANAGEMENT APIs =====
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/taxes', [TaxController::class, 'index']);
    Route::post('/taxes', [TaxController::class, 'store']);
    Route::put('/taxes/{id}', [TaxController::class, 'update']);
    Route::delete('/taxes/delete-multiple', [TaxController::class, 'deleteMultiple']);
    Route::delete('/taxes/{id}', [TaxController::class, 'destroy'])->where('id', '[0-9]+');
});