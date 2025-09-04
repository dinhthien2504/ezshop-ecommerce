<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AttributeValueController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


Route::get('/attributes', function () {
    return response()->json(['attributes'=> DB::table('tbl_attributes')->get()]);
});

Route::get('/attributes-with-values', [AttributeController::class, 'getAttributesWithValues']);
Route::get('/attributes-with-values-shop', [AttributeController::class, 'get_attribute_with_value_shop']);
Route::get('/attributes/{id}', [AttributeController::class, 'getAttributeById'])->where('id', '[0-9]+');
// ===== ADMIN ATTRIBUTE MANAGEMENT APIs =====
// Route::middleware(['auth:sanctum', 'permission:attribute'])->group(function () {
    
// });

Route::post('/attributes/shop', [AttributeController::class, 'store_for_shop']);
Route::post('/attributes', [AttributeController::class, 'store']);
Route::post('/attribute-values/{id}', [AttributeValueController::class, 'store'])->where('id', '[0-9]+');
Route::put('/attributes/{id}', [AttributeController::class, 'update'])->where('id', '[0-9]+');
Route::put('/attribute-values/{id}', [AttributeValueController::class, 'update'])->where('id', '[0-9]+');
Route::delete('/attributes/{id}', [AttributeController::class, 'destroy'])->where('id', '[0-9]+');
Route::delete('/attributes/delete-multiple', [AttributeController::class, 'deleteMultiple']);
Route::delete('/attribute-values/{id}', [AttributeValueController::class, 'destroy'])->where('id', '[0-9]+');
Route::delete('/attribute-values/delete-multiple', [AttributeValueController::class, 'deleteMultiple']);
// ===== PUBLIC ATTRIBUTE APIs =====

Route::get('/attribute_values/{id}', function ($attribute_id) {
    return response()->json(['attribute_values'=> DB::table('tbl_attribute_values')->where('attribute_id', $attribute_id)->get()]);
});

Route::get('/attribute-values/{id}', [AttributeValueController::class, 'getValuesByAttributeId'])->where('id', '[0-9]+');
Route::post('/attribute-values/{id}', [AttributeValueController::class, 'store'])->where('id', '[0-9]+');
Route::put('/attribute-values/{id}', [AttributeValueController::class, 'update'])->where('id', '[0-9]+');
Route::delete('/attribute-values/{id}', [AttributeValueController::class, 'destroy'])->where('id', '[0-9]+');
Route::delete('/attribute-values/delete-multiple', [AttributeValueController::class, 'deleteMultiple']);
Route::get('/attribute-values/product/{productId}', [AttributeValueController::class, 'getAttributesAndValuesByProduct'])->where('productId', '[0-9]+');