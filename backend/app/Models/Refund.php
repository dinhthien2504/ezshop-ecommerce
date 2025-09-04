<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $table = 'tbl_refunds';
    protected $fillable = [
        'order_id',
        'user_id',
        'address_id',
        'status',
        'total_refund',
    ];

    public function refund_items()
    {
        return $this->hasMany(RefundItem::class, 'refund_id');
    }
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
