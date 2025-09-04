<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('tbl_permissions')->insert([
            [
                'title' => 'dashboard',
                'description' => 'Dashboard quản trị',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'shop',
                'description' => 'Quản lý cửa hàng',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'category',
                'description' => 'Quản lý danh mục',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'user',
                'description' => 'Quản lý người dùng',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'staff',
                'description' => 'Quản lý nhân viên',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'product',
                'description' => 'Quản lý sản phẩm',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'attribute',
                'description' => 'Quản lý thuộc tính',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'finance',
                'description' => 'Quản lý tài chính',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'voucher',
                'description' => 'Quản lý mã giảm giá',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'rank',
                'description' => 'Quản lý hạng thành viên',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'setting',
                'description' => 'Cấu hình hệ thống',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'role',
                'description' => 'Quản lý vai trò',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'order',
                'description' => 'Quản lý đơn hàng',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
