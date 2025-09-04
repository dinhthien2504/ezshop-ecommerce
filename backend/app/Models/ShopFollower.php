<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopFollower extends Model
{
    protected $table = "tbl_shop_followers";
    protected $fillable = [
        'shop_id',
        'user_id',
    ];

    public $timestamps = false;

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
