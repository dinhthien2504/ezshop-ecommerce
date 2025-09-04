<?php

namespace App\Repositories;

use App\Models\Voucher;
use Carbon\Carbon;

class VoucherEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Voucher::class;
    }

    public function getAvailableVouchersForUser(int $userId, ?Carbon $appliedAt = null)
    {
        $appliedAt = $appliedAt ?? now();

        $vouchers = $this->_model->where('is_active', true)->get();

        return $vouchers->filter(function ($voucher) use ($userId, $appliedAt) {
            return $voucher->isValidAt($appliedAt)
                && $voucher->usedCountByUser($userId) < $voucher->limit
                && $voucher->quantity > 0;
        })->values();

    }

    public function decrementVoucherQuantities(array $voucherIds): int
    {
        return $this->_model
            ->whereIn('id', $voucherIds)
            ->where('quantity', '>', 0)
            ->decrement('quantity', 1);
    }

    public function getVouchersBySource(
        string $source,
        array $with = [],
        int $perPage = 15,
        ?string $search = null,
        ?int $month = null,
        ?int $year = null,
        ?string $status = null
    ) {
        $query = $this->_model
            ->with($with)
            ->where('source', $source)
            ->orderByDesc('created_at');


        if ($source === 'shop') {
            $user = auth()->user();
            $shop = $user->shop()->first();
            $shopId = $shop->id;
            $query->where('shop_id', $shopId);

        }

        if (!empty($search)) {
            $query->where('code', 'LIKE', '%' . $search . '%');
        }

        if (!empty($month) && !empty($year)) {
            $query->whereMonth('start_date', $month)
                ->whereYear('start_date', $year);
        }

        if (!empty($status)) {
            $now = now();

            switch ($status) {
                case 'active':
                    $query->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now)
                        ->where('quantity', '>', 0);
                    break;

                case 'upcoming':
                    $query->where('is_active', true)
                        ->where('start_date', '>', $now);
                    break;

                case 'out_of_stock':
                    $query->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now)
                        ->where('quantity', '<=', 0);
                    break;

                case 'expired':
                    $query->where('end_date', '<', $now);
                    break;

                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        $vouchers = $query->paginate($perPage);

        $vouchers->getCollection()->transform(function ($voucher) {
            $voucher->used_count = $voucher->usedCount();
            return $voucher;
        });

        return $vouchers;
    }

}