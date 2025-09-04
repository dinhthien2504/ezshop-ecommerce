<?php
use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::get("/addresses", [AddressController::class, 'addressByUser'])->middleware('auth:sanctum');
Route::post("/address", [AddressController::class, 'store'])->middleware('auth:sanctum');
Route::put("/address/{id}", [AddressController::class, 'update'])->middleware('auth:sanctum');
Route::post("/address/set-default/{id}", [AddressController::class, 'setDefaultAddress'])->middleware('auth:sanctum');
Route::get('/shop/address', [AddressController::class, 'getAddress']);