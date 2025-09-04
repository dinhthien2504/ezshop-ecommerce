<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Wallet::class;
    }

    public function getWalletPlatform()
    {
        return $this->_model->where('type', 'platform')->first();
    }

    public function getWalletByUser(int $userId)
    {
        return $this->_model->where('type', 'user')
            ->where('user_id', $userId)
            ->first();
    }

    public function updateBalance($walletId, $newBalance)
    {
        $wallet = $this->_model->find($walletId);
        if (!$wallet) {
            return null;
        }

        $wallet->balance = $newBalance;
        $wallet->save();

        return $wallet;
    }


}