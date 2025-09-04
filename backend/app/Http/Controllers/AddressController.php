<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\AddressService;
use App\Services\LocationService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function store(StoreAddressRequest $request)
    {
        try {
            $address = $this->addressService->createAddress($request);
            return response()->json([
                'message' => 'Thêm địa chỉ mới thành công',
                'address' => $address
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm địa chỉ mới thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateAddressRequest $request, $id)
    {
        try {
            $this->addressService->updateAddress($request, $id);
            return response()->json([
                'message' => 'Cập nhật địa chỉ thành công.'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Cập nhật địa chỉ thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function addressByUser()
    {
        try {
            $addresses = $this->addressService->getAddressByUser();
            return response()->json([
                'message' => 'Lấy địa chỉ thành công',
                'addresses' => $addresses
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy địa chỉ thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function setDefaultAddress($id)
    {
        try {
            $address = \App\Models\Address::findOrFail($id);
            $address->is_default = 1;
            $address->save();
            // Reset other addresses to not default
            \App\Models\Address::where('user_id', $address->user_id)
                ->where('id', '!=', $id)
                ->update(['is_default' => 0]);
            return response()->json([
                'message' => 'Thiết lập địa chỉ mặc định thành công.',
                'address' => $address
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thiết lập địa chỉ mặc định thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getAddress(Request $request, LocationService $locationService)
    {
        $provinceId = $request->query('province_id');
        $districtId = $request->query('district_id');
        $wardCode = $request->query('ward_code'); // ward_code là chuỗi

        $province = $locationService->getProvinces()->firstWhere('ProvinceID', (int) $provinceId);
        $districts = $locationService->getDistricts($provinceId);
        $district = $districts->firstWhere('DistrictID', (int) $districtId);
        $wards = $locationService->getWards($districtId);
        $ward = $wards->firstWhere('WardCode', $wardCode);

        return response()->json([
            'province' => $province['ProvinceName'] ?? null,
            'district' => $district['DistrictName'] ?? null,
            'ward'     => $ward['WardName'] ?? null,
        ]);
    }
}
