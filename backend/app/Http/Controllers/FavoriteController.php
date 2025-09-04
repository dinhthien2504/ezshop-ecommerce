<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\ProductEloquentRepository;
use App\Services\ProductService;
class FavoriteController extends Controller
{
    // Lấy danh sách yêu thích của người dùng đã đăng nhập
    public function index()
    {
        $user = auth()->user();

        $favorites = Favorite::where('user_id', $user->id)
            ->get();

        return response()->json([
            'message' => 'Danh sách yêu thích',
            'data' => $favorites
        ]);
    }

    // Thêm sản phẩm vào danh sách yêu thích
    public function store(Request $request)
    {
        $user = auth()->user();

        $favorite = Favorite::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'message' => 'Đã thêm vào yêu thích',
            'data' => $favorite
        ]);
    }

    // Xóa sản phẩm khỏi danh sách yêu thích
   public function destroy($product_id)
{
    $user = auth()->user();

    $favorite = Favorite::where('user_id', $user->id)
                        ->where('product_id', $product_id)
                        ->first();

    if (!$favorite) {
        return response()->json(['message' => 'Không tìm thấy yêu thích'], 404);
    }

    $favorite->delete();

    return response()->json([
        'message' => 'Đã xóa khỏi yêu thích',
        'data' => true
    ]);
}
public function checkWishlist(Request $request)
{
    $user = auth()->user();
    $exists = Favorite::where('user_id', $user->id)
        ->where('product_id', $request->product_id)
        ->exists();

    return response()->json([
        'is_following' => $exists
    ]);
}
public function getAllProductsWishlist(Request $request)
{
    $userId = auth()->id();
    $perPage = $request->input('per_page', 20);

    $products = app(ProductService::class)
        ->getAllProductsWishlist($userId, $perPage);

    return response()->json([
        'message' => 'Danh sách sản phẩm yêu thích',
        'data' => $products
    ]);
}
}

