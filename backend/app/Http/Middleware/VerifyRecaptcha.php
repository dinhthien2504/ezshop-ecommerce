<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http; 

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->validate([
            'recaptcha_token' => 'required',
        ]);

        $token = $request->input('recaptcha_token');
        
        if (!$token) {
            return response()->json(['message' => 'Thiếu mã reCAPTCHA'], 400);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret'),
            'response' => $request->recaptcha_token,
        ]);

        if (!$response->json('success')) {
            return response()->json(['message' => 'Xác minh reCAPTCHA thất bại'], 422);
        }

        return $next($request);
    }
}
