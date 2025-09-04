<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MockBankController extends Controller
{
    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'bin' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // Random 90% success, 10% fail
        $isSuccess = rand(1, 100) <= 90;

        if ($isSuccess) {
            return response()->json([
                'success' => true,
                'transaction_code' => 'TXN' . now()->format('YmdHis') . rand(100, 999),
                'message' => 'Transfer successful.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Transfer failed due to insufficient funds.',
        ], 400);
    }
}
