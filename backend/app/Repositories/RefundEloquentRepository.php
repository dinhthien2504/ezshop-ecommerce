<?php

namespace App\Repositories;

use App\Models\Refund;

class RefundEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Refund::class;
    }

    public function createRefund($data)
    {
        return $this->_model->create([
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'address_id' => $data['address_id'],
            'total_refund' => $data['total_refund'],
            'status' => $data['status'] ?? 'pending',
        ]);
    }
}
