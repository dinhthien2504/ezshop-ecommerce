<?php

use App\Http\Controllers\FeeController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN FEE MANAGEMENT APIs =====
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/fees', [FeeController::class, 'index']);
    Route::post('/fees', [FeeController::class, 'store']);
    Route::put('/fees/{id}', [FeeController::class, 'update']);
    Route::delete('/fees/delete-multiple', [FeeController::class, 'deleteMultiple']);
    Route::delete('/fees/{id}', [FeeController::class, 'destroy'])->where('id', '[0-9]+');
});