<?php

namespace App\Services;

use App\Repositories\OrderEloquentRepository;
use App\Repositories\RateEloquentRepository;
use App\Repositories\ShopEloquentRepository;
use App\Repositories\VisitEloquentRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ShopService
{
    protected $shopRepository;
    protected $orderRepository;
    protected $visitRepository;
    protected $rateRepository;
    protected $locationService;

    public function __construct(
        ShopEloquentRepository $shopRepository,
        OrderEloquentRepository $orderRepository,
        VisitEloquentRepository $visitRepository,
        RateEloquentRepository $rateRepository,
        LocationService $locationService
    ) {
        $this->shopRepository = $shopRepository;
        $this->orderRepository = $orderRepository;
        $this->visitRepository = $visitRepository;
        $this->rateRepository = $rateRepository;
        $this->locationService = $locationService;
    }

    public function getBankAccountInfo()
    {
        $shopId = auth()->user()->shop()->first()->id;
        if (!$shopId) {
            throw new \Exception("Shop không tồn tại.", 404);
        }
        return $this->shopRepository->getBankAccountInfo($shopId);
    }

    public function saveBankAccountInfo($request)
    {
        $shop = auth()->user()->shop;
        if (!$shop) {
            throw new \Exception("Shop không tồn tại.", 404);
        }
        $shop->bank_account_name = $request->bank_account_name;
        $shop->bank_account_number = $request->bank_account_number;
        $shop->bank_name = $request->bank_name;
        $shop->bank_short_name = $request->bank_short_name;
        $shop->bank_logo = $request->bank_logo;
        $shop->bin_account = $request->bin_account;

        $shop->save();

        return $shop;
    }

    public function getBalance()
    {
        $shop = auth()->user()->shop()->first();

        if (!$shop) {
            throw new \Exception("Shop không tồn tại.", 404);
        }
        return $this->shopRepository->calculateBalance($shop->id);
    }

    public function getStatistics()
    {
        $shopId = auth()->user()->shop()->first()->id;
        if (!$shopId) {
            throw new \Exception("Shop không tồn tại.", 404);
        }

        $orders = $this->orderRepository->getOrderByShopId($shopId);
        $pending = $orders->where('order_status', 1)->count();
        $confirmed = $orders->where('order_status', 2)->count();
        $done = $orders->where('order_status', 5)->count();
        $canceled = $orders->where('order_status', 6)->count();
        $return_refund = $orders->where('order_status', 7)->count();

        return [
            'pending' => $pending,
            'confirmed' => $confirmed,
            'done' => $done,
            'canceled' => $canceled,
            'return_refund' => $return_refund,
        ];
    }

    public function getAnalytics($request)
    {
        $start = Carbon::parse($request->start_date ?? now()->startOfMonth());
        $end = Carbon::parse($request->end_date ?? now()->endOfMonth());
        $shopId = auth()->user()->shop()->first()->id;

        $revenue = $this->orderRepository->getRevenue($shopId, $start, $end);
        $orders = $this->orderRepository->countOrders($shopId, $start, $end);
        $visits = $this->visitRepository->countVisits($shopId, $start, $end);

        $conversionRate = $visits > 0 ? round(($orders / $visits) * 100, 2) : 0;
        return [
            'total_revenue' => $revenue,
            'total_orders' => $orders,
            'total_visits' => $visits,
            'conversion_rate' => $conversionRate,
        ];
    }

    public function getChartAnalytics($request)
    {
        $start = Carbon::parse($request->start_date ?? now()->startOfMonth());
        $end = Carbon::parse($request->end_date ?? now()->endOfMonth());
        $shopId = auth()->user()->shop()->first()->id;

        $period = CarbonPeriod::create($start, $end);
        $dates = [];
        $revenues = [];
        $visits = [];
        $orders = [];

        foreach ($period as $date) {
            $formattedDate = $date->format('d/m');
            $dates[] = $formattedDate;
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $revenues[] = $this->orderRepository->getRevenue($shopId, $dayStart, $dayEnd);
            $visits[] = $this->visitRepository->countVisits($shopId, $dayStart, $dayEnd);
            $orders[] = $this->orderRepository->countOrders($shopId, $dayStart, $dayEnd);
        }

        return [
            'categories' => $dates,
            'series' => [
                [
                    'name' => 'Doanh số',
                    'data' => $revenues,
                ],
                [
                    'name' => 'Lượt truy cập',
                    'data' => $visits,
                ],
                [
                    'name' => 'Đơn hàng',
                    'data' => $orders,
                ],
            ]
        ];
    }

    public function getShopById($id)
    {
        $shop = $this->shopRepository->findShopWithRelations($id);

        $totalRates = $this->rateRepository->countShopRates($id);

        $shop->total_rates = $totalRates;
        $province_name = $this->locationService->getProvinceNameByDistrictId($shop->district_id);
        $shop->province_name = $province_name;

        return $shop;
    }
}