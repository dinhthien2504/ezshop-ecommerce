<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   protected $orderService;

   public function __construct(OrderService $orderService)
   {
      $this->orderService = $orderService;
   }

   public function index(Request $request)
   {
      try {
         $filters = $request->only([
            'page',
            'per_page',
            'search_code_order',
            'order_status',
         ]);
         $orders = $this->orderService->getOrders($filters);
         return response()->json($orders, 200);
      } catch (\Throwable $th) {
         return response()->json([
            'message' => 'Lấy danh sách đơn hàng thất bại.',
            'error' => $th->getMessage()
         ], 500);
      }
   }

   public function orderByUser()
   {
      try {
         $orders = $this->orderService->orderByUser();
         return response()->json([
            'orders' => $orders
         ]);
      } catch (\Throwable $th) {
         return response()->json([
            'message' => 'Lấy danh sách đơn hàng thất bại.',
            'error' => $th->getMessage()
         ], 500);
      }
   }

   public function orderByUserOrderId($id)
   {
      try {
         $order = $this->orderService->orderByUserOrderId($id);
         return response()->json([
            'order' => $order
         ], 200);
      } catch (\Throwable $th) {
         return response()->json([
            'message' => 'Lấy đơn hàng thất bại.',
            'error' => $th->getMessage()
         ], 500);
      }
   }

   public function getShopOrders(Request $request)
   {
      try {
         $orders = $this->orderService->getShopOrders($request);
         return response()->json($orders, 200);
      } catch (\Throwable $th) {
         return response()->json([
            'message' => 'Lấy đơn hàng thất bại.',
            'error' => $th->getMessage(),
         ], 500);
      }
   }

   public function changeStatus(Request $request, $id)
   {
      try {
         $order = $this->orderService->changeStatus($request, $id);
         return response()->json([
            'order_status' => $order->order_status,
            'message' => 'Thành công'
         ], 200);
      } catch (\Throwable $th) {
         return response()->json([
            'message' => 'Cập nhật trạng thái đơn hàng thất bại.',
            'error' => $th->getMessage()
         ], 500);
      }
   }
}
