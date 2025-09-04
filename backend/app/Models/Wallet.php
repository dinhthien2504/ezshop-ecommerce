<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'tbl_wallets';

    protected $fillable = [
        'user_id',
        'type',
        'balance',
        'debt',
        'created_at',
        'updated_at'
    ];

}
