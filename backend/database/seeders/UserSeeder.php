<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Tạo tài khoản admin với vai trò "Chúa"
        DB::table('tbl_users')->insert([
            'user_name' => 'Admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => $now,
            'password' => Hash::make('12345678'),
            'phone' => null,
            'avatar' => null,
            'role_id' => 2, // Role "Chúa"
            'rank_id' => null,
            'status' => 1, // Active
            'google_id' => null,
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
