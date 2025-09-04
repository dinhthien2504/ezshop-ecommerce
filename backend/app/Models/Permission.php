<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'tbl_permissions';

    protected $fillable = [
        'title',
        'description',
    ];

    protected $attributes = [
        'title' => '',
        'description' => '',
    ];
}
