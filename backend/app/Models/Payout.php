<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $table = 'tbl_payouts';
    protected $fillable = [
        'shop_id',
        'payout_code',
        'gross_amount',
        'platform_fee',
        'tax_amount',
        'vat_amount',
        'net_amount',
        'transaction_code',
        'payout_status',
        'requested_at',
        'processed_at',
        'processed_by',
        'approval_status',
        'reject_reason',
        'frozen_amount',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
    
}
