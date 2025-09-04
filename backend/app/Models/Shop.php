<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    protected $table = "tbl_shops";

    protected $fillable = [
        'owner_id',
        'shop_name',
        'pickup_name',
        'phone',
        'province_id',
        'district_id',
        'ward_code',
        'background',
        'image',
        'detail_address',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id');
    }

    public function followers()
    {
        return $this->hasMany(ShopFollower::class, 'shop_id');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class, 'shop_id');
    }
}
