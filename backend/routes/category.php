<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('categories-tree', [CategoryController::class, 'getCategoryTree']);
Route::get('/categories', function () {
    $categories = DB::table('tbl_categories')->get();
    return response()->json(['categories' => $categories]);
});
// ===== ADMIN CATEGORY MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:category'])->group(function () {
    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{id}', [CategoryController::class, 'update']);
    Route::delete('categories', [CategoryController::class, 'destroy']);
});

// ===== PUBLIC CATEGORY APIs =====
Route::get('categories/leaf-with-products/{category}', [CategoryController::class, 'getAvailableLeafCategories']);
Route::get('categories/root-with-products', [CategoryController::class, 'getRootCategoriesWithProducts']);
Route::get('categories/parents', [CategoryController::class, 'getRootCategories']);
Route::get('products/{id}/categories', [CategoryController::class, 'getCategoriesByProduct']);