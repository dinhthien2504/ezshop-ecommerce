<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use App\Services\RefundService;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    protected $refund_service;

    public function __construct(RefundService $refund_service)
    {
        $this->refund_service = $refund_service;
    }
    public function store(StoreRefundRequest $request)
    {
        try {
            $refund = $this->refund_service->createRefund($request);
            return response()->json($refund, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'erorr' => $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $refund = $this->refund_service->updateRefund($request, $id);

            return response()->json($refund, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
