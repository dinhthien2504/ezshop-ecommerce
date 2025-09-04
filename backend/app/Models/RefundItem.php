<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    protected $table = 'tbl_refund_items';
    protected $fillable = [
        'refund_id',
        'order_detail_id',
        'variant_id',
        'qty',
        'price_original',
        'subtotal',
        'discount_allocated',
        'refund_amount',
        'reason_type',
        'reason_detail',
        'evidences',
        'shipping_fee',
        'shipping_payer',
        'updated_at',
        'created_at',
    ];

    public function refund_item()
    {
        return $this->hasOne(RefundItem::class, 'order_detail_id');
    }
}
