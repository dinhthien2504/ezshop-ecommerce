<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\Media;
use App\Models\Category;
use App\Models\VariantAttribute;
use App\Models\AttributeValue;


class Product extends Model
{
    protected $table = 'tbl_products';
    protected $fillable = [
        'category_id',
        'shop_id',
        'name',
        'slug',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'created_at'
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    public function medias()
    {
        return $this->hasMany(Media::class, 'product_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
    
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function getAverageRatingAttribute()
    {
        $variantIds = $this->variants->pluck('id');

        $orderDetailIds = OrderDetail::whereIn('variant_id', $variantIds)->pluck('id');

        $avg = Rate::whereIn('order_detail_id', $orderDetailIds)->avg('rate') ?? 0;
        return number_format($avg, 1);
    }

    public function getDisplayPriceWithVatAttribute()
    {
        $variant = $this->variants->sortBy('price')->first();
        if (!$variant)
            return 0;

        $price = $variant->price;

        $vatPercent = $this->category?->tax?->vat_percent ?? 0;

        return round($price * (1 + $vatPercent / 100));
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'product_id');
    }
}
