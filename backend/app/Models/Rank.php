<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    protected $table = 'tbl_ranks';

    protected $fillable = [
        'name',
        'min_spent',
        'max_spent',
        'benefits',
    ];

    protected $attributes = [
        'name' => '',
        'min_spent' => 0,
        'max_spent' => 0,
        'benefits' => null,
    ];

}
