<?php

use App\Http\Controllers\RankController;
use Illuminate\Support\Facades\Route;

// ===== ADMIN RANK MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:rank'])->group(function () {
    Route::get('/rank', [RankController::class, 'getRanks']);
    Route::post('/rank', [RankController::class, 'store']);
    Route::put('/rank/{id}', [RankController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/rank/{id}', [RankController::class, 'destroy'])->where('id', '[0-9]+');
    Route::delete('/rank/delete-multiple', [RankController::class, 'deleteMultiple']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/rank/update-user-rank', [RankController::class, 'updateUserRank']);
});
