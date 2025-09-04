<?php

namespace App\Services;

use App\Repositories\VoucherEloquentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VoucherService
{
    protected $voucherRepository;

    public function __construct(VoucherEloquentRepository $voucherRepository)
    {
        $this->voucherRepository = $voucherRepository;
    }

    public function getAvailableVouchersForUser()
    {
        $user = auth()->user();
        return $this->voucherRepository->getAvailableVouchersForUser($user->id);
    }

    public function createVoucher($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->only([
                'shop_id',
                'code',
                'type',
                'source',
                'title',
                'description',
                'quantity',
                'limit',
                'min',
                'max',
                'percent',
                'is_active',
                'start_date',
                'end_date',
            ]);
            if ($data['source'] == 'shop') {
                $shopId = auth()->user()->shop()->first()->id;
                $data['shop_id'] = $shopId;
            }

            if ($data['type'] === 'fixed_amount') {
                $data['percent'] = null;
            }

            $data['is_active'] = $data['is_active'] ?? 1;

            $data['start_date'] = Carbon::parse($data['start_date']);
            $data['end_date'] = Carbon::parse($data['end_date']);

            return $this->voucherRepository->create($data);
        });
    }

    public function getVouchersBySource($source = 'platform', $request)
    {
        return $this->voucherRepository->getVouchersBySource(
            $source,
            [],
            $request->input('per_page'),
            $request->input('search'),
            $request->input('month'),
            $request->input('year'),
            $request->input('status')
        );
    }

    public function updateVoucher($request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $data = $request->only([
                'shop_id',
                'code',
                'type',
                'source',
                'title',
                'description',
                'quantity',
                'limit',
                'min',
                'max',
                'percent',
                'is_active',
                'start_date',
                'end_date',
            ]);

            if ($data['type'] === 'fixed_amount') {
                $data['percent'] = null;
            }

            $data['is_active'] = $data['is_active'] ?? 1;

            $data['start_date'] = Carbon::parse($data['start_date']);
            $data['end_date'] = Carbon::parse($data['end_date']);

            return $this->voucherRepository->update($id, $data);
        });
    }

    public function unactiveVoucher($id)
    {
        $voucher = $this->voucherRepository->find($id);

        if (!$voucher) {
            throw new \Exception('Voucher không tồn tại', 404);
        }
        $voucher->is_active = false;
        $voucher->save();
        return $voucher;
    }

}