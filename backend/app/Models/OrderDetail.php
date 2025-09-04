<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'tbl_order_details';
    protected $fillable = [
        'order_id',
        'variant_id',
        'quantity',
        'is_reviewed',
    ];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function rates()
    {
        return $this->hasMany(Rate::class, 'order_detail_id');
    }

    public function refundItems()
    {
        return $this->hasMany(RefundItem::class, 'order_detail_id', 'id');
    }
}
