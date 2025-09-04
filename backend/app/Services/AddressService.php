<?php

namespace App\Services;

use App\Repositories\AddressEloquentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AddressService
{
    protected $addressRepository;
    protected $ghnLocationService;

    public function __construct(
        AddressEloquentRepository $addressRepository,
        GhnLocationService $ghnLocationService
    ) {
        $this->addressRepository = $addressRepository;
        $this->ghnLocationService = $ghnLocationService;
    }

    public function createAddress(Request $request)
    {
        $user = $user = auth()->user();
        $dataAddress = [
            'user_id' => $user->id,
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'address_detail' => $request->input('address_detail'),
            'district_id' => $request->input('district_id'),
            'ward_code' => $request->input('ward_code'),
            'is_default' => 0
        ];
        $hasDefault = $this->addressRepository->userHasDefaultAddress($user->id);

        $dataAddress['is_default'] = $hasDefault ? ($data['is_default'] ?? 0) : 1;

        return $this->addressRepository->create($dataAddress);
    }

    public function updateAddress(Request $request, $id)
    {
        $address = $this->addressRepository->find($id);

        $data = $request->only([
            'name',
            'phone',
            'address_detail',
            'district_id',
            'ward_code',
            'is_default',
        ]);

        $data['is_default'] = $data['is_default'] ?? 0;

        $address->update($data);

        return $address;
    }

    public function getAddressByUser()
    {
        $user = auth()->user();

        $addresses = $this->addressRepository->getAddressByUser($user->id);

        // Lấy tỉnh, quận
        $provinces = $this->ghnLocationService->getProvinces();
        $districts = $this->ghnLocationService->getDistricts();

        $districtIds = $addresses->pluck('district_id')->unique()->filter();

        $wardMap = [];
        foreach ($districtIds as $districtId) {
            $wards = $this->ghnLocationService->getWardsByDistrict($districtId);
            $wardMap[$districtId] = $wards->keyBy('WardCode');
        }

        $mappedAddresses = $addresses->map(function ($address) use ($provinces, $districts, $wardMap) {
            $districtId = $address->district_id;
            $wardCode = (string) $address->ward_code;

            $district = $districts->firstWhere('DistrictID', $districtId);
            $province = $provinces->firstWhere('ProvinceID', $district['ProvinceID'] ?? null);
            $ward = $wardMap[$districtId][$wardCode] ?? null;

            return [
                'address_id' => $address->id,
                'name' => $address->name,
                'phone' => $address->phone,
                'address_detail' => $address->address_detail,
                'ward_code' => $wardCode,
                'ward' => $ward['WardName'] ?? null,
                'district_id' => $district['DistrictID'] ?? null,
                'district' => $district['DistrictName'] ?? null,
                'province_id' => $province['ProvinceID'] ?? null,
                'province' => $province['ProvinceName'] ?? null,
                'is_default' => $address->is_default,
            ];
        });

        return $mappedAddresses;
    }
}