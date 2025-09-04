<?php

use App\Http\Controllers\ViolationController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN VIOLATION TYPE MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:shop,setting'])->group(function () {
    Route::get('/violation-types/admin', [ViolationController::class, 'getTypesAdmin']);
    Route::get('/violations', [ViolationController::class, 'getViolations']);
    Route::post('/violation-types', [ViolationController::class, 'store']);
    Route::put('/violation-types/{id}', [ViolationController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/violations/change-status/{id}', [ViolationController::class, 'changeStatusViolation'])->where('id', '[0-9]+');
    Route::delete('/violation-types/{id}', [ViolationController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/violation-types/delete-multiple', [ViolationController::class, 'deleteMultiple']);
    Route::delete('/violations/{id}', [ViolationController::class, 'destroyViolation'])->where('id', '[0-9]+');
    Route::delete('/violations/delete-multiple', [ViolationController::class, 'deleteMultipleViolations']);
});

// ===== PUBLIC/USER VIOLATION APIs =====
Route::get('/violation-types', [ViolationController::class, 'getViolationTypes']);
Route::post('/send-report', [ViolationController::class, 'sendReport'])->middleware('auth:sanctum');
Route::get('/violation-shops', [ViolationController::class, 'getViolationShops']);
