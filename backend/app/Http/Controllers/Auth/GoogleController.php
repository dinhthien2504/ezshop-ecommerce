<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google_Client;
use App\Models\User;
use Illuminate\Support\Str;


class GoogleController extends Controller
{
    public function handle(Request $request)
    {
        $id_token = $request->input('id_token');

        $client = new Google_Client(['client_id' => config('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($id_token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid ID token'], 401);
        }

        $google_id = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];

        // Check if user exists
        $user = User::where('email', $email)->first();

        if ($user) {
            if (!$user->google_id) {
                $user->google_id = $google_id;
                $user->save();
            }
        } else {
            // Create new user
            $user = User::create([
                'user_name' => $name,
                'email' => $email,
                'google_id' => $google_id,
                'password' => bcrypt(Str::random(16)), // Random password
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('Google Token')->plainTextToken;
        $user->load('shop');
        return response()->json([
            'token' => $token,
            'user_name' => $user->user_name,
            'avatar' => $user->avatar,
            'google_id' => $user->google_id,
            'role' => $user->role,
            'shop_id' => $user->shop->id ?? 0,
            'message' => 'Đăng nhập thành công'
        ]);
    }
}
