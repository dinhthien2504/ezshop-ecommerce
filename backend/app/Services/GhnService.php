<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GhnService
{
    protected $token;
    protected $shopId;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
        $this->shopId = config('services.ghn.shop_id');
    }

    public function getAvailableServices($fromDistrictId, $toDistrictId)
    {
        $cacheKey = "ghn_available_services_{$fromDistrictId}_{$toDistrictId}";

        return Cache::remember($cacheKey, 3600, function () use ($fromDistrictId, $toDistrictId) {
            $response = Http::withHeaders([
                'Token' => $this->token,
            ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/available-services', [
                        "shop_id" => (int) $this->shopId,
                        "from_district" => $fromDistrictId,
                        "to_district" => $toDistrictId
                    ]);

            return $response->successful() ? $response->json('data') : null;
        });
    }

    public function calculateShippingFee(
        $fromDistrictId,
        $toDistrictId,
        $fromWardCode = null,
        $toWardCode = null,
        $weight = 1000,
        $length = 10,
        $width = 10,
        $height = 10
    ) {
        $cacheKey = "ghn_shipping_fee_" . md5(json_encode([
            $fromDistrictId,
            $toDistrictId,
            $fromWardCode,
            $toWardCode,
            $weight,
            $length,
            $width,
            $height
        ]));

        return Cache::remember($cacheKey, 3600, function () use ($fromDistrictId, $toDistrictId, $fromWardCode, $toWardCode, $weight, $length, $width, $height) {
            $services = $this->getAvailableServices($fromDistrictId, $toDistrictId);

            if (!$services || count($services) == 0) {
                return [
                    'error' => true,
                    'message' => 'Không tìm thấy dịch vụ giao hàng phù hợp.',
                    'Dữ liệu lấy được' => $services
                ];
            }

            $serviceId = $services[0]['service_id'];
            $serviceTypeId = $services[0]['service_type_id'];

            $payload = [
                "from_district_id" => $fromDistrictId,
                "to_district_id" => $toDistrictId,
                "weight" => $weight,
                "length" => $length,
                "width" => $width,
                "height" => $height,
                "service_id" => $serviceId,
                "service_type_id" => $serviceTypeId,
                "insurance_value" => 0,
                "coupon" => null,
            ];

            if ($fromWardCode)
                $payload["from_ward_code"] = $fromWardCode;
            if ($toWardCode)
                $payload["to_ward_code"] = $toWardCode;

            $response = Http::withHeaders([
                'Token' => $this->token,
                'ShopId' => $this->shopId,
                'Content-Type' => 'application/json',
            ])->post('https://online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee', $payload);

            return $response->json();
        });
    }
}
