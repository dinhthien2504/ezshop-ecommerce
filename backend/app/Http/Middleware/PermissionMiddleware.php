<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Kiểm tra role_id cho admin routes
        if ($request->is('api/admin/*') && !$user->role_id) {
            return response()->json([
                'message' => 'Forbidden',
                'error' => 'no_role'
            ], 403);
        }

        // Lấy quyền của user
        $userPermissions = RolePermission::where('role_id', $user->role_id)
            ->where('permission_value', 1)
            ->with('permission')
            ->get()
            ->pluck('permission.title')
            ->toArray();

        // Kiểm tra quyền
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return response()->json([
                    'message' => 'Forbidden',
                    'required_permission' => $permission,
                    'user_permissions' => $userPermissions
                ], 403);
            }
        }

        return $next($request);
    }
}
