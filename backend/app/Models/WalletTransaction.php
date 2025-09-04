<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $table = 'tbl_wallet_transactions';

    protected $fillable = [
        'wallet_id',
        'order_id',
        'type',
        'amount',
        'balance_after',
        'note',
        'created_at'
    ];
    public $timestamps = false;
}
