<?php

namespace App\Repositories;

use App\Models\Shop;

class ShopEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Shop::class;
    }

    public function getBankAccountInfo($shopId)
    {
        $bankInfo = $this->_model->where('id', $shopId)
            ->select(
                'bank_account_name',
                'bank_account_number',
                'bank_name',
                'bank_short_name',
                'bank_logo',
                'bin_account'
            )
            ->first();
        if (
            is_null($bankInfo->bank_account_name) &&
            is_null($bankInfo->bank_account_number) &&
            is_null($bankInfo->bank_name) &&
            is_null($bankInfo->bank_short_name) &&
            is_null($bankInfo->bank_logo) &&
            is_null($bankInfo->bin_account)
        ) {
            return null;
        }
        return $bankInfo;
    }

    public function calculateBalance($shopId)
    {
        // Lấy danh sách đơn hàng đủ điều kiện
        $orders = \DB::table('tbl_orders')
            ->select('id', 'amount_to_seller')
            ->where('shop_id', $shopId)
            ->where('payment_status', 'paid')
            ->where('order_status', 5)
            ->whereNull('payout_id')
            ->get();

        $orderIds = $orders->pluck('id')->toArray();
        if (empty($orderIds)) {
            return 0;
        }

        $totalBalance = $orders->sum('amount_to_seller') ?? 0;

        return [
            'order_ids' => $orderIds,
            'total_balance' => $totalBalance
        ];
    }

    public function findShopWithRelations($id)
    {
        return $this->_model::withCount(['products', 'followers'])
            ->findOrFail($id);
    }

}