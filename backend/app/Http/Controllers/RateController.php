<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRateRequest;
use App\Services\RateService;
use Illuminate\Http\JsonResponse;

class RateController extends Controller
{
    protected RateService $rateService;

    public function __construct(RateService $rateService)
    {
        $this->rateService = $rateService;
    }

    public function store(StoreRateRequest $request): JsonResponse
    {
        try {
            $this->rateService->storeFeedbacks($request);

            return response()->json(['message' => 'Đánh giá thành công']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lỗi hệ thống',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function getRatesByProductId($productId)
    {
        $perPage = 5;
        $query = \App\Models\Rate::with(['user', 'orderDetail.productVariant'])
            ->whereHas('orderDetail.productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            })
            ->where('rate_status', 1);

        $rates = $query->paginate($perPage);

        $ratesData = $rates->map(function ($rate) {
            return [
                'id' => $rate->id,
                'user' => [
                    'id' => $rate->user->id,
                    'name' => $rate->user->user_name,
                    'avatar' => $rate->user->avatar,
                ],
                'order_detail_id' => $rate->order_detail_id,
                'variant_id' => $rate->orderDetail->variant_id,
                'rate' => $rate->rate,
                'images' => json_decode($rate->images, true),
                'content' => $rate->content,
                'created_at' => $rate->created_at,
            ];
        });

        return response()->json([
            'rates' => $ratesData,
            'current_page' => $rates->currentPage(),
            'per_page' => $rates->perPage(),
            'last_page' => $rates->lastPage(),
            'total' => $rates->total(),
        ]);
    }
}
