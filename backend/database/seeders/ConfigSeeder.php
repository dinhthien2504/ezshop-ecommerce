<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_config')->insert([
            'logo_header' => 'ezlogo1.png',
            'logo_footer' => 'ezlogo2.png',
            'main_color' => '#d52220',
            'title' => 'EzShop',
            'description' => 'Sàn thương mại điện tử hàng đầu Việt Nam',
            'email' => 'ezshop@gmail.com',
            'phone' => '0123456789',
            'address' => '123 Đường ABC, TP.HCM',
            'facebook' => 'https://facebook.com/ezshop',
            'instagram' => 'https://instagram.com/ezshop',
            'youtube' => 'https://youtube.com/ezshop',
            'tiktok' => 'https://tiktok.com/@ezshop',
            'twitter' => 'https://twitter.com/ezshop',
            'is_active' => true,
        ]);
    }
}
