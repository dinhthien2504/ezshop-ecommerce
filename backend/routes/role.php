<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN ROLE & PERMISSION MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:role'])->group(function () {
    Route::get('/role', [RoleController::class, 'getRoles']);
    Route::get('/role/permissions/{id}', [RoleController::class, 'getPermissions'])->where('id', '[0-9]+');
    Route::get('/all-role', [RoleController::class, 'getAllRoles']);
    Route::post('/role', [RoleController::class, 'store']);
    Route::post('/role/permissions/{id}', [RoleController::class, 'updatePermissions'])->where('id', '[0-9]+');
    Route::post('/role/change-status/{id}', [RoleController::class, 'changeStatus'])->where('id', '[0-9]+');
    Route::put('/role/{id}', [RoleController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/role/{id}', [RoleController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/role/delete-multiple', [RoleController::class, 'deleteMultiple']);
});

Route::get('/role/user', [RoleController::class, 'getRoleUser'])->middleware(['auth:sanctum']);
