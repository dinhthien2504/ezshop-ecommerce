<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tbl_taxes')->insert([
            [
                'name' => 'Thuế GTGT 10%',
                'vat_percent' => 10.0,
                'tax_percent' => 2.0,
                'description' => 'Áp dụng cho hầu hết hàng hóa và dịch vụ',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
