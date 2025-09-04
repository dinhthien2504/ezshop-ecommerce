<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    protected $table = 'tbl_vouchers';
    protected $fillable = [
        'shop_id',
        'code',
        'type',
        'source',
        'title',
        'description',
        'quantity',
        'limit',
        'min',
        'max',
        'percent',
        'is_active',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
    ];

    public function isValidAt(Carbon $time)
    {
        return $this->is_active && $time->between($this->start_date, $this->end_date);
    }

    public function usedCountByUser(int $userId)
    {
        $usedTimes = DB::table('tbl_order_applied_vouchers AS oav')
            ->join('tbl_orders AS o', 'o.id', '=', 'oav.order_id')
            ->where('oav.voucher_id', $this->id)
            ->where('o.user_id', $userId)
            ->select(DB::raw('DATE_FORMAT(oav.applied_at, "%Y-%m-%d %H:%i:%s") as used_time'))
            ->distinct()
            ->pluck('used_time');
        return $usedTimes->count();
    }


    public function wasUsedByUserAt(int $userId, Carbon $appliedAt): bool
    {
        return DB::table('tbl_order_applied_vouchers AS oav')
            ->join('tbl_orders AS o', 'o.id', '=', 'oav.order_id')
            ->where('oav.voucher_id', $this->id)
            ->where('o.user_id', $userId)
            ->whereDate('oav.applied_at', $appliedAt->toDateString())
            ->exists();
    }

    public function usages()
    {
        return $this->hasMany(OrderAppliedVoucher::class, 'voucher_id');
    }

    public function usedCount()
    {
        $usedTime = DB::table('tbl_order_applied_vouchers AS oav')
            ->join('tbl_orders AS o', 'o.id', '=', 'oav.order_id')
            ->where('oav.voucher_id', $this->id)
            ->select(DB::raw('DATE_FORMAT(oav.applied_at, "%Y-%m-%d %H:%i:%s") as used_time'))
            ->distinct()
            ->pluck('used_time');
        return $usedTime->count();
    }
}
