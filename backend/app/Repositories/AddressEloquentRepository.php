<?php

namespace App\Repositories;

use App\Models\Address;


class AddressEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Address::class;
    }

    public function getAddressByUser($user_id)
    {
        return $this->_model->where('user_id', $user_id)->get();
    }

    public function userHasDefaultAddress(int $userId): bool
    {
        return $this->_model::where('user_id', $userId)
            ->where('is_default', 1)
            ->exists();
    }
}