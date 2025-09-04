<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'tbl_role_permissions';

    protected $fillable = [
        'role_id',
        'permission_id',
        'permission_value',
    ];

    protected $attributes = [
        'role_id' => 0,
        'permission_id' => 0,
        'permission_value' => true,
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
