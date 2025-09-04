<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wallet;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        Wallet::create([
            'user_id' => null,
            'type' => 'platform',   
            'balance'    => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
