<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\VerifyRecaptcha;


Route::post('/login', [UserController::class, 'login']);

Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'register']);

Route::post('/register-verify', [UserController::class, 'register'])
    ->middleware(VerifyRecaptcha::class)
    ->name('register.verify');

Route::get('/user', function (Request $request) {
    return response()->json(['user' => $request->user()]);
})->middleware('auth:sanctum');

Route::get('/shop', function (Request $request) {
    $shop = DB::table('tbl_shops')->where('owner_id', $request->user()->id)->first();
    if ($shop) {
        return response()->json(['shop_id' => $shop->id], 200);
    } else {
        return response()->json(['message' => 'Shop not found'], 200);
    }
})->middleware('auth:sanctum');

Route::post('/auth/google-login', [GoogleController::class, 'handle'])
    ->name('auth.google-login');

// ===== ADMIN USER MANAGEMENT APIs =====
Route::middleware(['auth:sanctum', 'permission:user,staff'])->group(function () {
    Route::get('/user-list', [UserController::class, 'getUsers']);
    Route::post('/user/change-status/{id}', [UserController::class, 'changeStatus'])->where('id', '[0-9]+');
    Route::post('/user/lock-multiple', [UserController::class, 'lockMultiple']);
    Route::post('/user/unlock-multiple', [UserController::class, 'unlockMultiple']);
});

Route::middleware(['auth:sanctum', 'permission:staff'])->group(function () {
    Route::get('/staff-list', [UserController::class, 'getStaffs']);
    Route::post('/staff/change-role/{id}', [UserController::class, 'changeRole']);
    Route::post('/staff/add', [UserController::class, 'storeStaff']);
});

// ===== USER PROFILE APIs (Authenticated users) =====
Route::get('/user-profile', [UserController::class, 'getUserProfile'])->middleware('auth:sanctum');
Route::post('/user-profile', [UserController::class, 'updateUserProfile'])->middleware('auth:sanctum');
Route::post('/user/validate-password', [UserController::class, 'validatePassword'])->middleware('auth:sanctum');
Route::post('/user/change-password', [UserController::class, 'changePassword'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/email/send-otp', [VerifyEmailController::class, 'sendVerifyEmail']);
Route::middleware('auth:sanctum')->post('/email/verify-otp', [VerifyEmailController::class, 'verifyOtp']);

