<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $table = 'tbl_visits';
    protected $fillable = [
        'product_id',
        'shop_id',
        'type',
        'ip_address',
        'user_agent',
        'visited_at',
    ];
    public $timestamps = false;
}

