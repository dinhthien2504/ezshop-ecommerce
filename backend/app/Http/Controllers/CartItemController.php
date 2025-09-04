<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCartItemRequest;
use App\Services\CartItemService;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    protected $cartItemService;

    public function __construct(CartItemService $cartItemService)
    {
        $this->cartItemService = $cartItemService;
    }

    public function getUserCart()
    {
        try {
            $carts = $this->cartItemService->getCartUser();
            return response()->json($carts, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy thông tin giỏ hàng thất bại.',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function store(StoreCartItemRequest $request)
    {
        try {
            $data = $request->only(['variant_id', 'quantity']);

            $result = $this->cartItemService->createCartItem($data);
            return response()->json([
                'message' => $result['message'],
                'cart_id' => $result['cart_id'] ?? null,
            ], $result['code']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Thêm sản phẩm vào giỏ hàng thất bại.',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $result = $this->cartItemService->updateCartItem($id, $request->only(['variant_id', 'quantity']));

            return response()->json([
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ], $result['code']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Chỉnh sửa giỏ hàng thất bại.',
                'erorr' => $th->getMessage()
            ], 500);
        }

    }

    public function destroy(Request $request)
    {
        try {
            $result = $this->cartItemService->deleteCartItems($request->input('cartIds', []));

            return response()->json([
                'message' => $result['message']
            ], $result['code']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Xóa giỏ hàng thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function getUserCartForHeader()
    {
        try {
            $result = $this->cartItemService->getUserCartForHeader();

            return response()->json([
                'carts' => $result['data'],
                'message' => $result['message']
            ], $result['code']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Lấy giỏ hàng thất bại.',
                'error' => $th->getMessage()
            ], 500);
        }

    }
}
