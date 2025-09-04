<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public function provinces(): JsonResponse
    {
        try {
            $data = $this->locationService->getProvinces();
            return response()->json(['provinces' => $data], 200);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'error' => true,
                    'message' => 'Lấy tỉnh/thành thất bại'
                ],
                500
            );
        }
    }

    public function districts($provinceId): JsonResponse
    {
        try {
            $data = $this->locationService->getDistricts($provinceId);
            return response()->json(['districts' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => 'Lấy quận/huyện thất bại'], 500);
        }
    }

    public function wards($districtId): JsonResponse
    {
        try {
            $data = $this->locationService->getWards($districtId);
            return response()->json(['wards' => $data]);
        } catch (\Throwable $th) {
            return response()->json(['error' => true, 'message' => 'Lấy phường/xã thất bại'], 500);
        }
    }
}
