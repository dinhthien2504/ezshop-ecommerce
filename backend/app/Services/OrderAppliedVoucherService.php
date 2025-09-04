<?php

namespace App\Services;

use App\Repositories\OrderAppliedVoucherEloquentRepository;
class OrderAppliedVoucherService
{
    protected $orderAppliedVoucherRepository;

    public function __construct(OrderAppliedVoucherEloquentRepository $orderAppliedVoucherRepository)
    {
        $this->orderAppliedVoucherRepository = $orderAppliedVoucherRepository;
    }

    public function addVoucherToOrder(array $vouchers, $order_id, $shop_id)
    {
        $voucherBulk = [];
        foreach ($vouchers as $voucher) {
            if ($voucher['shop_id'] && $voucher['shop_id'] != $shop_id)
                continue;
            $voucherBulk[] = [
                'voucher_id' => $voucher['id'],
                'order_id' => $order_id,
                'type' => $voucher['type'],
                'shop_id' => $voucher['shop_id'],
                'discount_amount' => $voucher['discount'],
                'applied_at' => now()
            ];
        }
        return $this->orderAppliedVoucherRepository->insertMultiple($voucherBulk);
    }
}