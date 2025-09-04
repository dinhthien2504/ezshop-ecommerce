<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\SearchImageController;
use Illuminate\Support\Facades\Route;

Route::get('/products/provinces', [ProductController::class, 'getAvailableProvinces'])
    ->name('products.provinces-by-product');

Route::get('products/home-page', [ProductController::class, 'getHomepageProducts']);
Route::get('products/top-view', [ProductController::class, 'getTopViewedProducts']);
Route::get('products/related', [ProductController::class, 'getRelatedProducts']);
Route::get('products/{id}', [ProductController::class, 'getProductDetail']);
Route::get('product-shop', [ProductController::class, 'get_product_shop'])->middleware('auth:sanctum');
Route::get('product', [ProductController::class, 'get_product'])->middleware('auth:sanctum');

Route::post('products/find-variant/{productId}', [ProductController::class, 'getVariantIdByAttributeValues']);
Route::post('/product', [ProductController::class, 'store']);
Route::post('/product/update', [ProductController::class, 'update']);
Route::post('/product/update/shutdown', [ProductController::class, 'update_shutdown_status']);
Route::post('/product/update/pending', [ProductController::class, 'update_pending_status']);

Route::get('/shop/product/{id}', [ProductController::class, 'get_shop_by_product'])->where('id', '[0-9]+');

Route::get('products', [ProductController::class, 'index'])
    ->name('products.index');

Route::post('/products/search-by-image', [SearchImageController::class, 'searchByImage'])
    ->name('product.search-by-image');

// ===== ADMIN PRODUCT MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:product'])->group(function () {
    Route::get('/product-all', [ProductController::class, 'getAllProducts']);
    Route::post('/product/change-status/{id}', [ProductController::class, 'changeStatus'])->where('id', '[0-9]+');
    Route::post('/product/change-multiple', [ProductController::class, 'changeMultiple']);
});

// ===== PUBLIC/SHOP PRODUCT APIs =====
