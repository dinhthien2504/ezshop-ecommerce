<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequestPayoutRequest;
use App\Services\PayoutService;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    protected $payout_service;

    public function __construct(PayoutService $payout_service)
    {
        $this->payout_service = $payout_service;
    }

    public function index(Request $request)
    {
        try {
            $payouts = $this->payout_service->getPayouts($request);
            return response()->json([
                'data' => $payouts->items(),
                'meta' => [
                    'current_page' => $payouts->currentPage(),
                    'last_page' => $payouts->lastPage(),
                    'per_page' => $payouts->perPage(),
                    'total' => $payouts->total(),
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getPayoutsByShop()
    {
        try {
            $payouts = $this->payout_service->getPayoutsByShopId();
            return response()->json($payouts, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function store(RequestPayoutRequest $request)
    {
        try {
            $result = $this->payout_service->requestPayout($request);

            return response()->json([
                'message' => 'Yêu cầu rút tiền thành công.',
                'data' => $result,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Yêu cầu rút tiền thất bại',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function processPayout($id)
    {
        try {
            $payout = $this->payout_service->processPayout($id);

            return response()->json($payout, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xử lý thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rejectPayout($id, Request $request)
    {
        try {
            $payout = $this->payout_service->rejectPayout($id, $request);

            return response()->json($payout, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xử lý thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    public function retryPayout($id)
    {
        try {
            $payout = $this->payout_service->retryPayout($id);

            return response()->json($payout, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Xử lý thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

}