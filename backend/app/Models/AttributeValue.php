<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'tbl_attribute_values';

    protected $fillable = [
        'attribute_id',
        'value',
    ];

    protected $attributes = [
        'attribute_id' => null,
        'value' => '',
    ];

    public $timestamps = true;

    //Tạo mối quan hệ với bảng thuộc tính 1 - N
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    //1 - N
    public function variantAttribute()
    {
        return $this->hasMany(VariantAttribute::class, 'attribute_value_id');
    }
}
