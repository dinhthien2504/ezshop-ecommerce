<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GhnLocationService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
    }

    public function getProvinces()
    {
        return Cache::remember('ghn_provinces', now()->addHours(24), function () {
            $res = Http::withHeaders(['Token' => $this->token])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');
            return collect($res->json('data') ?? []);
        });
    }

    public function getDistricts()
    {
        return Cache::remember('ghn_districts', now()->addHours(24), function () {
            $res = Http::withHeaders(['Token' => $this->token])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
            return collect($res->json('data') ?? []);
        });
    }

    public function getWardsByDistrict($districtId)
    {
        return Cache::remember("ghn_wards_district_{$districtId}", now()->addHours(24), function () use ($districtId) {
            $res = Http::withHeaders(['Token' => $this->token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                    'district_id' => $districtId
                ]);
            return collect($res->json('data') ?? []);
        });
    }

    public function getFullAddress($addressDetail, $districtId, $wardCode)
    {
        $provinces = $this->getProvinces();
        $districts = $this->getDistricts();
        $wards = $this->getWardsByDistrict($districtId);

        $ward = $wards->firstWhere('WardCode', (string) $wardCode);
        $district = $districts->firstWhere('DistrictID', $districtId);
        $province = $provinces->firstWhere('ProvinceID', $district['ProvinceID'] ?? null);

        return $addressDetail . ', ' .
            ($ward['WardName'] ?? '') . ', ' .
            ($district['DistrictName'] ?? '') . ', ' .
            ($province['ProvinceName'] ?? '');
    }
}