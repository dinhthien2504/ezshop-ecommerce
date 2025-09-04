<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Services\VoucherService;
use Illuminate\Http\Request;
class VoucherController extends Controller
{
    protected $voucherService;

    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    public function getVouchersPlatform(Request $request)
    {
        try {
            $vouchers = $this->voucherService->getVouchersBySource('platform', $request);
            return response()->json([
                'data' => $vouchers->items(),
                'meta' => [
                    'current_page' => $vouchers->currentPage(),
                    'last_page' => $vouchers->lastPage(),
                    'per_page' => $vouchers->perPage(),
                    'total' => $vouchers->total(),
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy mã giảm giá thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getVouchersByShop(Request $request)
    {
        try {
            $vouchers = $this->voucherService->getVouchersBySource('shop', $request);
            return response()->json([
                'data' => $vouchers->items(),
                'meta' => [
                    'current_page' => $vouchers->currentPage(),
                    'last_page' => $vouchers->lastPage(),
                    'per_page' => $vouchers->perPage(),
                    'total' => $vouchers->total(),
                ],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy mã giảm giá thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getAvailableVouchersForUser()
    {
        try {
            $vouchersActive = $this->voucherService->getAvailableVouchersForUser();
            return response()->json([
                'vouchers' => $vouchersActive
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy mã giảm giá thất bại.',
                'error' => $th->getMessage(),
            ], 500);
        }

    }

    public function store(StoreVoucherRequest $request)
    {
        try {
            $voucher = $this->voucherService->createVoucher($request);
            return response()->json($voucher, 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạo mã giảm giá thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(UpdateVoucherRequest $request, $id)
    {
        try {
            $voucher = $this->voucherService->updateVoucher($request, $id);
            return response()->json($voucher, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạo mã giảm giá thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function unactive($id)
    {
        try {
            $voucher = $this->voucherService->unactiveVoucher($id);
            return response()->json($voucher, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Tạm khóa mã giảm giá thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
