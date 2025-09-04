<?php

namespace App\Repositories;

use App\Models\OrderAppliedVoucher;

class OrderAppliedVoucherEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return OrderAppliedVoucher::class;
    }


}