<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'tbl_address';
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address_detail',
        'district_id',
        'ward_code',
        'is_default'
    ];
}
