<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use App\Services\RefundItemService;
use Illuminate\Http\Request;

class RefundItemController extends Controller
{
    protected $refund_item_service;

    public function __construct(RefundItemService $refund_item_service)
    {
        $this->refund_item_service = $refund_item_service;
    }
    
    public function calculateRefundItemAmount(Request $request)
    {
        try {
            $result = $this->refund_item_service->calculateRefundAmountForItems($request);
            return response()->json($result, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'erorr' => $th->getMessage()
            ]);
        }
    }
}
