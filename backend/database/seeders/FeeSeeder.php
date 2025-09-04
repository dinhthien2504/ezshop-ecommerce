<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_fees')->insert([
            [
                'name' => 'Phí vận chuyển',
                'percentage' => 5.00, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phí thu hộ (COD)',
                'percentage' => 1.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phí xử lý đơn hàng',
                'percentage' => 2.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Phí hoa hồng sàn',
                'percentage' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
