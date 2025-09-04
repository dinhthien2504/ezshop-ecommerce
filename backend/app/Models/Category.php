<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'tbl_categories';
    protected $fillable = [
        'name',
        'image',
        'description',
        'parent_id',
        'tax_id',
        'fee_id'
    ];
    //Tạo mối quan hệ với bảng sản phẩm 1 - N
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
    
    public function fee()
    {
        return $this->belongsTo(Fee::class, 'fee_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    //Off created_at & update_at 
    public $timestamps = false;
}
