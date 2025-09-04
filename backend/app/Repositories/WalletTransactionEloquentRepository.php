<?php

namespace App\Repositories;

use App\Models\WalletTransaction;

class WalletTransactionEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return WalletTransaction::class;
    }

    public function getTransactions(
        int $perPage = 20,
        ?string $type = null,
        ?int $month = null,
        ?int $year = null,
        ?string $search = null
    ) {
        $query = $this->_model->orderByDesc('created_at');

        if ($type) {
            $query->where('type', $type);
        }
        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('note', 'like', "%$search%")
                    ->orWhere('amount', 'like', "%$search%")
                    ->orWhere('id', $search)
                    ->orWhere('order_id', $search);
            });
        }

        return $query->paginate($perPage);
    }

    public function createTransaction($walletId, $amount, $type, $balanceAfter, $note = null, $orderId = null)
    {
        return $this->_model->create([
            'wallet_id' => $walletId,
            'order_id' => $orderId,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'note' => $note,
        ]);
    }
}