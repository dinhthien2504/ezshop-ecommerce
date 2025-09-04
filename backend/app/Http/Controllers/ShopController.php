<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveBankAccountRequest;
use App\Http\Requests\StoreShopRequest;
use App\Models\Shop;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Services\ShopService;

class ShopController extends Controller
{
    protected $shopService;
    protected $notification_service;

    public function __construct(ShopService $shopService, NotificationService $notification_service)
    {
        $this->shopService = $shopService;
        $this->notification_service = $notification_service;
    }

    public function store(StoreShopRequest $request)
    {
        try {

            $user = auth()->user();
            $shop = Shop::create([
                'owner_id' => $user->id,
                'shop_name' => $request->shop_name,
                'pickup_name' => $request->name,
                'phone' => $request->phone,
                'detail_address' => $request->detail_address,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_code' => $request->ward_code,
            ]);

            return response()->json([
                'shop_id' => $shop->id,
                'message' => 'Chúc mừng bạn đã tạo Shop thành công!.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getShopById($id)
    {
        try {
            $shop = $this->shopService->getShopById($id);

            return response()->json($shop, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
    }

    public function getBankAccountInfo()
    {
        try {
            $bank_info = $this->shopService->getBankAccountInfo();
            return response()->json($bank_info);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function saveBankAccountInfo(SaveBankAccountRequest $request)
    {
        try {
            $bankInfo = $this->shopService->saveBankAccountInfo($request);
            return response()->json($bankInfo, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getBalance()
    {
        try {
            $balance = $this->shopService->getBalance();
            return response()->json($balance, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function get_shop(Request $request)
    {
        $user = auth()->user();
        $shop = Shop::where('owner_id', $user->id)->first();
        if (!$shop) {
            return response()->json(['message' => 'Shop not found'], 404);
        }
        return response()->json(['shop' => $shop], 200);
    }

    public function updateShop(Request $request)
    {
        try {
            $shop = auth()->user()->shop;

            // Cập nhật tên shop, mô tả nếu có
            if ($request->has('shop_name')) {
                $shop->shop_name = $request->input('shop_name');
            }
            if ($request->has('description')) {
                $shop->description = $request->input('description');
            }

            // Xử lý logo mới nếu có
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '_logo_' . $logo->getClientOriginalName();
                $frontendPath = base_path('../frontend/public/imgs/shops/' . $logoName);
                $logo->move(dirname($frontendPath), basename($frontendPath));
                $shop->image = $logoName;
            }

            // Xử lý background mới nếu có
            if ($request->hasFile('background')) {
                $background = $request->file('background');
                $bgName = time() . '_bg_' . $background->getClientOriginalName();
                $frontendPath = base_path('../frontend/public/imgs/shops/' . $bgName);
                $background->move(dirname($frontendPath), basename($frontendPath));
                $shop->background = $bgName;
            }

            $shop->save();

            return response()->json(['message' => 'Cập nhật shop thành công', 'shop' => $shop], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cập nhật thất bại', 'error' => $e->getMessage()], 500);
        }
    }

    public function getStatistics()
    {
        try {
            $statistics = $this->shopService->getStatistics();
            return response()->json($statistics);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getAnalytics(Request $request)
    {
        try {
            $analytics = $this->shopService->getAnalytics($request);
            return response()->json($analytics);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getChartAnalytics(Request $request)
    {
        try {
            $chart_analytics = $this->shopService->getChartAnalytics($request);
            return response()->json($chart_analytics);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $shop = Shop::find($id);
        $shop->status = $request->input('status');
        $shop->save();
        if ($shop->status == 0) {
            $this->notification_service->createNotification([
                'title' => "Shop bạn đã bị khóa",
                'message' => "Shop của bạn đã bị khóa. Vui lòng liên hệ với chúng tôi để biết thêm chi tiết.",
                'send_type' => 'to_shop',
                'receiver_ids' => [$shop->owner_id]
            ]);
        } elseif ($shop->status == 1) {
            $this->notification_service->createNotification([
                'title' => "Shop bạn đã được mở khóa",
                'message' => "Shop của bạn đã được mở khóa. Cảm ơn bạn đã hợp tác với chúng tôi.",
                'send_type' => 'to_shop',
                'receiver_ids' => [$shop->owner_id]
            ]);
        }
        return response()->json([
            'message' => 'Đổi trạng thái thành công'
        ]);
    }

    public function changeMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['message' => 'Không có ID nào được chọn'], 400);
        }

        try {
            $shops = Shop::whereIn('id', $ids)->get();
            foreach ($shops as $shop) {
                $shop->status = $request->input('status');
                if ($shop->status == 0) {
                    $this->notification_service->createNotification([
                        'title' => "Shop bạn đã bị khóa",
                        'message' => "Shop của bạn đã bị khóa. Vui lòng liên hệ với chúng tôi để biết thêm chi tiết.",
                        'send_type' => 'to_shop',
                        'receiver_ids' => [$shop->owner_id]
                    ]);
                } elseif ($shop->status == 1) {
                    $this->notification_service->createNotification([
                        'title' => "Shop bạn đã được mở khóa",
                        'message' => "Shop của bạn đã được mở khóa. Cảm ơn bạn đã hợp tác với chúng tôi.",
                        'send_type' => 'to_shop',
                        'receiver_ids' => [$shop->owner_id]
                    ]);
                }
                $shop->save();
            }
            return response()->json(['message' => 'Thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Lỗi'], 500);
        }
    }

    public function getShops(Request $request)
    {
        $search = $request->input('search', '');
        $perPage = 8;
        $query = Shop::query();
        if ($search) {
            $query->where('shop_name', 'like', '%' . $search . '%');
        }
        $shops = $query->paginate($perPage);

        return response()->json([
            'shops' => $shops->items(),
            'current_page' => $shops->currentPage(),
            'per_page' => $shops->perPage(),
            'last_page' => $shops->lastPage(),
            'total' => $shops->total(),
        ]);
    }
}
