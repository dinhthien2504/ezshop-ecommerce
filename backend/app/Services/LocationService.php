<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LocationService
{
    protected $token;

    public function __construct()
    {
        $this->token = config('services.ghn.token');
    }

    public function getProvinces()
    {
        return Cache::remember('ghn_provinces', 86400, function () {
            $res = Http::withHeaders(['Token' => $this->token])
                ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

            return collect($res->successful() ? $res->json('data') : null);
        });
    }

    public function getDistricts($provinceId)
    {
        return Cache::remember("ghn_districts_{$provinceId}", 86400, function () use ($provinceId) {
            $res = Http::withHeaders(['Token' => $this->token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/district', [
                    'province_id' => (int) $provinceId,
                ]);

            return collect($res->successful() ? $res->json('data') : null);
        });
    }

    public function getWards($districtId)
    {
        return Cache::remember("ghn_wards_{$districtId}", 86400, function () use ($districtId) {
            $res = Http::withHeaders(['Token' => $this->token])
                ->post('https://online-gateway.ghn.vn/shiip/public-api/master-data/ward', [
                    'district_id' => (int) $districtId,
                ]);

            return collect($res->successful() ? $res->json('data') : null);
        });
    }

    public function getProvinceNameByDistrictId($districtId)
    {
        $provinces = $this->getProvinces();

        foreach ($provinces as $province) {
            $districts = $this->getDistricts($province['ProvinceID']);

            $district = $districts->firstWhere('DistrictID', (int) $districtId);
            if ($district) {
                return $province['ProvinceName'];
            }
        }

        return null;
    }

    public function mapDistrictsToProvinces(array $districtIds): array
    {
        $provinceDistrictMap = [];

        $provinces = $this->getProvinces();
        foreach ($provinces as $province) {
            $districts = $this->getDistricts($province['ProvinceID']);
            foreach ($districts as $district) {
                if (in_array($district['DistrictID'], $districtIds)) {
                    $provinceDistrictMap[$province['ProvinceID']] = $province['ProvinceName'];
                }
            }
        }

        return $provinceDistrictMap;
    }

}
