<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            'Kích thước',
            'Màu sắc', 
            'Size',
            'Chất liệu',
            'Dung lượng',
            'Dung tích',
            'SPF',
            'Trọng lượng',
            'Hình dạng',
            'Switch',
            'Motor',
            'Số lượng',
            'Thành phần',
            'Trọng lượng tối đa',
            'Kích thước laptop'
        ];

        foreach ($attributes as $attribute) {
            DB::table('tbl_attributes')->insert([
                'name' => $attribute,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
