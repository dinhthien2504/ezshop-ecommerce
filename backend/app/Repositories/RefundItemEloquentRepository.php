<?php

namespace App\Repositories;

use App\Models\RefundItem;

class RefundItemEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return RefundItem::class;
    }
    
    public function createRefundItem($data)
    {
        return $this->_model->create([
            'refund_id'         => $data['refund_id'],
            'order_detail_id'   => $data['order_detail_id'],
            'variant_id'        => $data['variant_id'],
            'qty'               => $data['qty'],
            'price_original'    => $data['price_original'],
            'subtotal'          => $data['subtotal'],
            'discount_allocated'=> $data['discount_allocated'] ?? 0,
            'refund_amount'     => $data['refund_amount'],
            'reason_type'       => $data['reason_type'] ?? 'other',
            'reason_detail'     => $data['reason_detail'] ?? null,
            'evidences'         => $data['evidences'] ?? null,
            'shipping_fee'      => $data['shipping_fee'] ?? 0,
            'shipping_payer'    => $data['shipping_payer'] ?? 'seller',
        ]);
    }
}
