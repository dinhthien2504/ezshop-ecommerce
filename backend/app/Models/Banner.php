<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'tbl_banners';

    protected $fillable = [
        'title',
        'image',
        'link',
        'position',
        'priority',
        'start_at',
        'end_at',
    ];

    protected $attributes = [
        'title' => '',
        'image' => '',
        'link' => '',
        'position' => null,
        'priority' => 0,
        'start_at' => null,
        'end_at' => null,
    ];

    public $timestamps = true;
}
