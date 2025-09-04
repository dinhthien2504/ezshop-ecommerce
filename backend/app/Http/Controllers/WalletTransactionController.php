<?php

namespace App\Http\Controllers;

use App\Services\WalletTransactionService;
use Illuminate\Http\Request;

class WalletTransactionController extends Controller
{
    protected $WalletTransactionService;

    public function __construct(WalletTransactionService $WalletTransactionService)
    {
        $this->WalletTransactionService = $WalletTransactionService;
    }

    public function history(Request $request)
    {
        try {
            $transactions = $this->WalletTransactionService->getHistory($request);
            return response()->json([
                'data' => $transactions->items(),
                'meta' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}