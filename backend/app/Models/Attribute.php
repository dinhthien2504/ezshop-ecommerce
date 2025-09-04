<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'tbl_attributes';

    protected $fillable = [
        'name', 'shop_id'
    ];

    protected $attributes = [
        'name' => '',
    ];

    public $timestamps = true;

    // Tạo mối quan hệ với bảng thuộc tính 1 - N
    public function attributeValues()
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
