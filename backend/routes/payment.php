<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('payment-methods', function () {
    return response()->json([
        'message' => 'Lấy phương thức thanh toán thành công.',
        'payment_methods' => DB::table('tbl_payment_methods')->where('is_active', 1)->get()
    ]);
});

// Init Payment
Route::post('/payment/init', [PaymentController::class, 'initPayment'])->middleware('auth:sanctum');

//VNPAY
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');


//MoMo
Route::get('/payment/momo/return', [PaymentController::class, 'momoReturn'])->name('momo.return');
Route::post('/payment/momo/ipn', [PaymentController::class, 'handleMomoIpn'])->name('momo.ipn');
