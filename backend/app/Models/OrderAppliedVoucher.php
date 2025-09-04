<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAppliedVoucher extends Model
{
    protected $table = 'tbl_order_applied_vouchers';

    protected $fillable = [
        'voucher_id',
        'order_id ',
        'type',
        'shop_id ',
        'discount_amount ',
        'applied_at'
    ];

    public $timestamps = false;
}
