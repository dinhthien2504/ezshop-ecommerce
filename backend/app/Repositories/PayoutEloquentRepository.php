<?php

namespace App\Repositories;

use App\Models\Payout;
use Illuminate\Support\Facades\DB;

class PayoutEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Payout::class;
    }

    public function getPayoutsByShopId(int $shopId)
    {
        return $this->_model::where('shop_id', $shopId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getEligibleOrders(array $orderIds, $shopId)
    {
        return DB::table('tbl_orders')
            ->where('shop_id', $shopId)
            ->whereIn('id', $orderIds)
            ->where('payment_status', 'paid')
            ->where('order_status', 5)
            ->whereNull('payout_id')
            ->select('id', 'amount_to_seller', 'total_vat_amount')
            ->get();
    }

    public function createPayout(array $data)
    {
        return $this->_model::insertGetId($data);
    }

    public function attachPayoutToOrders(array $orderIds, $payoutId)
    {
        return DB::table('tbl_orders')
            ->whereIn('id', $orderIds)
            ->update(['payout_id' => $payoutId]);
    }

    public function getFilteredPayouts($filters)
    {
        $query = $this->_model::query()->with('shop', 'processedByUser');
        // Tìm kiếm
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('payout_code', 'like', '%' . $search . '%');
        }

        if (!empty($filters['payout_status'])) {
            $query->where('payout_status', $filters['payout_status']);
        }

        if (!empty($filters['approval_status'])) {
            $query->where('approval_status', $filters['approval_status']);
        }
        // Phân trang
        $perPage = $filters['per_page'] ?? 10;

        return $query->latest()->paginate($perPage);
    }
}