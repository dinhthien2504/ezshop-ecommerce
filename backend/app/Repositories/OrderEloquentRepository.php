<?php

namespace App\Repositories;

use App\Models\Order;

class OrderEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Order::class;
    }

    public function getOrders($filters = [])
    {
        $query = $this->_model::with([
            'user',
            'address',
            'shop',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.variantAttribute.attributeValue.attribute',
            'orderDetails.refundItems',
            'refund',
        ]);

        if (!empty($filters['order_status'])) {
            $query->where('order_status', $filters['order_status']);
        }

        if (!empty($filters['search_code_order'])) {
            $query->where('id', $filters['search_code_order']);
        }

        $query->orderBy('created_date', 'desc');

        // Phân trang
        $perPage = !empty($filters['per_page']) ? (int) $filters['per_page'] : 10;
        return $query->paginate($perPage);
    }

    public function getOrdersByUserId($userId)
    {
        return $this->_model::where('user_id', $userId)
            ->with([
                'address',
                'shop',
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.variantAttribute.attributeValue.attribute',
            ])
            ->orderBy('created_date', 'desc')
            ->get();
    }

    public function getOrderByUserOrderId($order_id, $user_id)
    {
        return $this->_model::where('id', $order_id)
            ->where('user_id', $user_id)
            ->with([
                'shop',
                'paymentMethod',
                'address',
                'orderAppliedVoucher',
                'orderDetails.productVariant.product',
                'orderDetails.productVariant.variantAttribute.attributeValue.attribute',
                'orderDetails.refundItems',
                'refund',
            ])
            ->firstOrFail();
    }

    public function getOrderByShopId($shopId)
    {
        return $this->_model::where('shop_id', $shopId)->get();
    }

    public function getOrdersByShopAndStatus($shop_id, $order_status)
    {
        return $this->_model::with([
            'shop',
            'paymentMethod',
            'address',
            'refund',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.variantAttribute',
            'orderDetails.refundItems',
        ])
            ->where('shop_id', $shop_id)
            ->where('order_status', $order_status)
            ->get();
    }

    public function getRevenue($shopId, $start, $end)
    {
        return $this->_model::where('shop_id', $shopId)
            ->whereBetween('created_date', [$start, $end])
            ->where('shop_id', $shopId)
            ->sum('amount_to_seller');
    }

    public function countOrders($shopId, $start, $end)
    {
        return $this->_model::where('shop_id', $shopId)
            ->whereBetween('created_date', [$start, $end])
            ->count();
    }

    public function getDeliveredOrdersOverDays($days = 7)
    {
        return $this->_model
            ->where('order_status', 4)
            ->where('updated_at', '<=', now()->subDays($days))
            ->get();
    }

}