<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = 'tbl_taxes';
    protected $fillable = [
        'name',
        'vat_percent',
        'tax_percent',
        'description'
    ];
    public function categories()
    {
        return $this->hasMany(Category::class, 'tax_id');
    }

}
