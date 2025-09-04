<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Tạo 2 role
        DB::table('tbl_roles')->insert([
            [
                'id' => 1,
                'title' => 'Không Có Quyền',
                'description' => 'Không có quyền nào',
                'role_status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'title' => 'Chúa',
                'description' => 'Có tất cả các quyền',
                'role_status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Gán tất cả quyền cho role id = 2
        $permissions = DB::table('tbl_permissions')->pluck('id');
        $rolePermissions = [];
        foreach ($permissions as $permissionId) {
            $rolePermissions[] = [
                'role_id' => 2,
                'permission_id' => $permissionId,
                'permission_value' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($rolePermissions)) {
            DB::table('tbl_role_permissions')->insert($rolePermissions);
        }
        // Không gán quyền nào cho role id = 2
    }
}
