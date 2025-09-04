<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'tbl_config';
    protected $fillable = [
        'logo_header',
        'logo_footer',
        'main_color',
        'title',
        'description',
        'email',
        'phone',
        'address',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'twitter',
        'is_active'
    ];

    protected $attributes = [
        'logo_header' => '',
        'logo_footer' => '',
        'main_color' => '#d52220',
        'title' => 'EzShop',
        'description' => 'Sàn thương mại điện tử hàng đầu Việt Nam',
        'email' => '',
        'phone' => '',
        'address' => '',
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'tiktok' => '',
        'twitter' => '',
        'is_active' => true,
    ];

    public $timestamps = false; // Tắt tự động updated_at, created_at
}
