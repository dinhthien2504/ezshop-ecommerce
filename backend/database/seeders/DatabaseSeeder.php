<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PSpell\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AttributeSeeder::class,
            AttributeValueSeeder::class,
            PaymentMethod::class,
            ConfigSeeder::class,
            RankSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ViolationTypes::class,
            WalletSeeder::class,
        ]);
    }
}
