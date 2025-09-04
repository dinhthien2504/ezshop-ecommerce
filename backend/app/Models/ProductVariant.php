<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\VariantAttribute;

class ProductVariant extends Model
{
    protected $table = 'tbl_product_variants';
    protected $fillable = [
        'stock',
        'price',
        'product_id',
        'sku',
        'image',
        'sell'
    ];

    //1 - N
    public function variantAttribute(){
        return $this->hasMany(VariantAttribute::class, 'variant_id');
    }

    //N - 1
    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'variant_id');
    }

}
