<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'tbl_roles';

    protected $fillable = [
        'title',
        'description',
        'role_status',
    ];

    protected $attributes = [
        'title' => '',
        'description' => '',
        'role_status' => true,
    ];

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }
}
