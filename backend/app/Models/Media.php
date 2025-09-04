<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;


class Media extends Model
{
    protected $table = 'tbl_medias';
    protected $fillable = [
        'product_id',
        'url',
        'is_main',
        'type',        
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id');
    }
}
