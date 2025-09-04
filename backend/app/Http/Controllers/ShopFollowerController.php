<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShopFollower;
use Illuminate\Support\Facades\Auth;

class ShopFollowerController extends Controller
{
    public function checkFollow(Request $request)
    {
        $user = Auth::user();
        $exists = ShopFollower::where('user_id', $user->id)
            ->where('shop_id', $request->shop_id)
            ->exists();

        return response()->json([
            'is_following' => $exists
        ]);
    }

    public function followShop(Request $request)
    {
        $user = Auth::user();

        $follow = ShopFollower::where('user_id', $user->id)
            ->where('shop_id', $request->shop_id)
            ->first();

        if ($follow) {
            // Nếu đã follow => hủy follow
            $follow->delete();
            return response()->json([
                'message' => 'Đã hủy theo dõi cửa hàng',
            ]);
        } else {
            // Nếu chưa follow => tạo mới
            ShopFollower::create([
                'user_id' => $user->id,
                'shop_id' => $request->shop_id,
            ]);

            return response()->json([
                'message' => 'Đã theo dõi cửa hàng',
            ]);
        }
    }

    public function getFollowingShops(Request $request)
    {
        $user = Auth::user();
        
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 6);
        
        $following = ShopFollower::where('user_id', $user->id)
            ->with(['shop' => function($query) {
                $query->select([
                    'id', 
                    'shop_name', 
                    'image', 
                    'status',
                    'province_id',
                    'district_id', 
                    'ward_code',
                    'detail_address',
                    'created_at'
                ]);
            }])
            ->paginate($perPage, ['*'], 'page', $page);

        // Lấy thống kê cho mỗi shop
        foreach ($following->items() as $follow) {
            if ($follow->shop) {
                // Đếm số sản phẩm
                $follow->shop->products_count = $follow->shop->products()->count();
                
                // Đếm số người theo dõi
                $follow->shop->followers_count = $follow->shop->followers()->count();
                
                // Đếm số đánh giá (nếu có bảng rates)
                $follow->shop->rates_count = 0; // Tạm thời để 0, có thể cập nhật sau
            }
        }

        return response()->json([
            'data' => $following->items(),
            'meta' => [
                'current_page' => $following->currentPage(),
                'last_page' => $following->lastPage(),
                'per_page' => $following->perPage(),
                'total' => $following->total(),
                'from' => $following->firstItem(),
                'to' => $following->lastItem()
            ]
        ]);
    }
}
