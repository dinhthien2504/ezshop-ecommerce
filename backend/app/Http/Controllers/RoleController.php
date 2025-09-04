<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Models\Permission;
use App\Models\RolePermission;
use PhpParser\Node\Stmt\TryCatch;

class RoleController extends Controller
{

    public function getRoles(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 5;
        $query = Role::query();

        $query->whereNotIn('id', [1, 2]);

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $roles = $query->paginate($perPage);

        // Lấy danh sách role_id
        $roleIds = collect($roles->items())->pluck('id')->all();

        // Lấy các quyền đang hoạt động cho từng role, kèm thông tin permission
        $rolePermissions = \App\Models\RolePermission::with('permission')
            ->whereIn('role_id', $roleIds)
            ->where('permission_value', 1)
            ->get()
            ->groupBy('role_id');

        $rolesWithPermissions = collect($roles->items())->map(function ($role) use ($rolePermissions) {
            $permissions = $rolePermissions[$role->id] ?? collect();
            return [
                'id' => $role->id,
                'title' => $role->title,
                'description' => $role->description,
                'role_status' => $role->role_status,
                'permissions' => $permissions->map(function ($perm) {
                    return [
                        'permission_id' => $perm->permission_id,
                        'permission_value' => $perm->permission_value,
                        'title' => $perm->permission->title ?? '',
                        'description' => $perm->permission->description ?? '',
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'roles' => $rolesWithPermissions,
            'current_page' => $roles->currentPage(),
            'per_page' => $roles->perPage(),
            'last_page' => $roles->lastPage(),
            'total' => $roles->total(),
        ]);
    }

    public function getPermissions($role_id)
    {
        $allPermissions = Permission::all();

        $rolePermissions = RolePermission::where('role_id', $role_id)
            ->pluck('permission_value', 'permission_id')
            ->toArray();

        $permissions = $allPermissions->map(function ($permission) use ($rolePermissions) {
            return [
                'id' => $permission->id,
                'permission_id' => $permission->id,
                'title' => $permission->description,
                'permission_value' => $rolePermissions[$permission->id] ?? 0,
            ];
        });

        return response()->json([
            'permissions' => $permissions,
        ]);
    }

    public function getRoleUser() {
        try {
            $user = auth()->user();
            $role = Role::find($user->role_id);
            
            // Lấy các quyền có permission_value = 1
            $permissions = RolePermission::with('permission')
                ->where('role_id', $user->role_id)
                ->where('permission_value', 1)
                ->get()
                ->map(function ($rolePermission) {
                    return [
                        'permission_id' => $rolePermission->permission_id,
                        'title' => $rolePermission->permission->title ?? '',
                        'description' => $rolePermission->permission->description ?? '',
                        'permission_value' => $rolePermission->permission_value,
                    ];
                });

            return response()->json([
                'role' => $role,
                'permissions' => $permissions
            ]);
        } catch (\Throwable $th) {
            Log::error('Error fetching role for user: ' . $th->getMessage());
            return response()->json(['message' => 'Error fetching role'], 500);
        }
    }

    public function updatePermissions(Request $request, $role_id)
    {
        $data = $request->input('permissions');

        foreach ($data as $item) {
            $permissionId = $item['id'];
            $value = $item['value'];

            $rolePermission = RolePermission::where('role_id', $role_id)
                ->where('permission_id', $permissionId)
                ->first();

            if ($rolePermission) {
                // Nếu đã tồn tại, cập nhật giá trị
                $rolePermission->permission_value = $value;
                $rolePermission->save();
            } else if ($value == 1) {
                // Nếu chưa tồn tại và được bật => tạo mới
                RolePermission::create([
                    'role_id' => $role_id,
                    'permission_id' => $permissionId,
                    'permission_value' => 1,
                ]);
            }
            // Nếu chưa tồn tại và value == 0 => không làm gì
        }

        return response()->json([
            'message' => 'Cập nhật quyền thành công',
        ]);
    }

    public function changeStatus($id)
    {
        $role = Role::find($id);
        if ($role->role_status === 1) {
            $role->role_status = 0;
        } else {
            $role->role_status = 1;
        }

        $role->save();

        return response()->json([
            'message' => 'Đổi trạng thái thành công'
        ]);
    }

    public function store(StoreRoleRequest $request)
    {
        // Lấy dữ liệu đã validate
        $data = $request->validated();

        // Tạo mới vai trò
        $role = Role::create($data);

        return response()->json([
            'message' => 'Thêm vai trò thành công',
            'role' => $role
        ]);
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            Log::info('Đã xóa role với ID: ' . $id);
            return response()->json(['message' => 'role deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa role: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa role'], 500);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $roles = Role::whereIn('id', $ids)->get();
            foreach ($roles as $role) {
                $role->delete();
            }
            Log::info('Đã xóa nhiều role với ID: ' . implode(', ', $ids));
            return response()->json(['message' => 'Đã xóa các role thành công'], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xóa nhiều role: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi xóa các role'], 500);
        }
    }

    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $role->title = $request->input('title');
            $role->description = $request->input('description');
            $role->save();
            return response()->json(['message' => 'role updated successfully', 'role' => $role], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật role: ' . $e->getMessage());
            return response()->json(['message' => 'Lỗi khi cập nhật role'], 500);
        }
    }

    public function getAllRoles()
    {
        $query = Role::all();
        return response()->json([
            'roles' => $query
        ]);
    }
}
