<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;

// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//     $request->fulfill();

//     // Redirect về frontend sau xác minh
//     return redirect('http://localhost:5173/dang-nhap?verified=true');
// })->middleware(['signed'])->name('verification.verify');

Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = User::findOrFail($id);

    // Kiểm tra hash hợp lệ
    if (! hash_equals(sha1($user->email), $hash)) {
        abort(403, 'Liên kết không hợp lệ.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    return redirect('http://localhost:5173/dang-nhap?verified=true');
})->middleware(['signed'])->name('verification.verify');