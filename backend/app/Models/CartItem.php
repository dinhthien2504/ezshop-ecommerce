<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'tbl_cart_items';

    protected $fillable = [
        'user_id',
        'variant_id',
        'quantity'
    ];
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    public $timestamps = false;
}
