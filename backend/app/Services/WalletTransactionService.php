<?php

namespace App\Services;

use App\Repositories\WalletTransactionEloquentRepository;

class WalletTransactionService
{
    protected $repo;

    public function __construct(WalletTransactionEloquentRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getHistory($request)
    {
        $perPage = $request->input('per_page', 20);
        $type = $request->input('type');
        $month = $request->input('month');
        $year = $request->input('year');
        $search = $request->input('search');

        return $this->repo->getTransactions($perPage, $type, $month, $year, $search);
    }

    
}