<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $table = 'tbl_violation_types';
    protected $fillable = [
        'code',
        'name'
    ];

    protected $attributes = [
        'code' => '',
        'name' => ''
    ];
}
