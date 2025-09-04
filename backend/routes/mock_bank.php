<?php
use App\Http\Controllers\MockBankController;

Route::post('/mock-bank/transfer', [MockBankController::class, 'transfer'])
    ->name('mock-bank.transfer');
