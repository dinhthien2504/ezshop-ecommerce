<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\VerifyEmail;
use Illuminate\Support\Str;

class VerifyEmailController extends Controller
{
    public function sendVerifyEmail(Request $request)
    {   
        
        $user = auth()->user();
        $key = 'verify_email_' . $user->id;

        // Tạo mã mới mỗi lần gửi
        $otp = rand(100000, 999999);
        Cache::put($key, $otp, now()->addMinutes(5));

        // Gửi mail
        switch ($request->type) {
            case 'verify_email':
                $subject = 'Xác thực địa chỉ email';
                break;
            case 'change_password':
                $subject = 'Xác nhận thay đổi mật khẩu';
                break;
            
        }

        Mail::to($user->email)->send(new VerifyEmail($otp, $subject));

        return response()->json([
            'success' => true,
            'message' => 'Mã xác thực mới đã được gửi.',
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $user = auth()->user();
        $key = 'verify_email_' . $user->id;

        if (Cache::get($key) == $request->otp) {
            $user->email_verified_at = now();
            $user->save();
            Cache::forget($key);

            return response()->json(['success' => true, 'message' => 'Xác thực email thành công.']);
        }

        return response()->json(['success' => false, 'message' => 'Mã xác thực không đúng hoặc đã hết hạn.'], 400);
    }
}
