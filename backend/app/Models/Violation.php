<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $table = 'tbl_violations';
    protected $fillable = [
        'reporter_id',
        'shop_id',
        'violation_type_id',
        'description',
        'status'
    ];

    protected $attributes = [
        'reporter_id' => null,
        'shop_id' => null,
        'violation_type_id' => null,
        'description' => '',
        'status' => null,
    ];

    public function type()
    {
        return $this->belongsTo(ViolationType::class, 'violation_type_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
