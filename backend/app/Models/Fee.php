<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'tbl_fees';

    protected $fillable = [
        'name',
        'percentage',
    ];
    public function categories(){
    return $this->hasMany(Category::class, 'fee_id');

    }

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class);

    // }
}
