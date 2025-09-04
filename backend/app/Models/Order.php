<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Order extends Model
{
    protected $table = 'tbl_orders';
    protected $fillable = [
        'user_id',
        'shop_id',
        'payment_method_id',
        'address_id',
        'total_amount',
        'order_status',
        'amount_to_seller',
        'platform_fee_amount',
        'tax_amount',
        'total_vat_amount',
        'payment_status',
        'shipping_fee',
        'created_date'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function orderAppliedVoucher()
    {
        return $this->hasMany(OrderAppliedVoucher::class, 'order_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'order_id');
    }
}
