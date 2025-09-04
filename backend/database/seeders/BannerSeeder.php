<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_banners')->insert([
            [
                'title' => 'Banner Trang Chủ 1',
                'image' => 'banner1.jpg',
                'link' => 'https://example.com/banner1',
                'position' => 'home_1',
                'priority' => 1,
                'start_at' => Carbon::now()->subDays(1),
                'end_at' => Carbon::now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Slider Khuyến Mãi',
                'image' => 'banner2.jpg',
                'link' => 'https://example.com/slider',
                'position' => 'slider',
                'priority' => 2,
                'start_at' => Carbon::now(),
                'end_at' => Carbon::now()->addDays(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Banner Ẩn Test',
                'image' => 'banner3.jpg',
                'link' => null,
                'position' => null,
                'priority' => 0,
                'start_at' => null,
                'end_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Banner Trang Chủ 2',
                'image' => 'banner4.jpg',
                'link' => 'https://example.com/banner2',
                'position' => 'home_2',
                'priority' => 3,
                'start_at' => Carbon::now()->subDays(5),
                'end_at' => Carbon::now()->addDays(20),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
