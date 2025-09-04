<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ViolationTypes extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_violation_types')->insert([
            ['code' => 'spam', 'name' => 'Nội dung spam'],
            ['code' => 'scam', 'name' => 'Gian lận'],
            ['code' => 'abuse', 'name' => 'Lăng mạ / quấy rối'],
            ['code' => 'illegal', 'name' => 'Nội dung vi phạm pháp luật'],
            ['code' => 'other', 'name' => 'Lý do khác'],
        ]);
    }
}
