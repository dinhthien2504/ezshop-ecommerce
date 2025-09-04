<?php

namespace App\Services;

use App\Repositories\OrderEloquentRepository;
use App\Repositories\RefundItemEloquentRepository;
use App\Services\GhnService;

class RefundItemService
{
    protected $refund_item_rep;
    protected $order_rep;
    protected $ghn_service;

    public function __construct(
        RefundItemEloquentRepository $refund_item_rep,
        OrderEloquentRepository $order_rep,
        GhnService $ghn_service,
    ) {
        $this->refund_item_rep = $refund_item_rep;
        $this->order_rep = $order_rep;
        $this->ghn_service = $ghn_service;
    }

    public function createRefundItem()
    {

    }

    public function calculateRefundAmountForItems($request)
    {
        $order_id = $request->input('order_id');
        $items = $request->input('items', []);

        $order = $this->order_rep->find($order_id);
        if (!$order) {
            throw new \Exception("Order Not Found", 404);
        }
        if (!$order->relationLoaded('orderDetails')) {
            $order->load('orderDetails.productVariant.product');
        }
        if (!$order->relationLoaded('orderAppliedVoucher')) {
            $order->load('orderAppliedVoucher');
        }
        if (!$order->relationLoaded('shop')) {
            $order->load('shop');
        }
        if (!$order->relationLoaded('address')) {
            $order->load('address');
        }

        // Tổng tiền sản phẩm ban đầu
        $orderTotal = $order->orderDetails->sum(fn($d) => $d->quantity * $d->productVariant->price);

        // Voucher & phí ship gốc
        $platformVoucher = $order->orderAppliedVoucher->firstWhere('type', 'platform');
        $shopVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shop');
        $shippingVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shipping');

        $platformDiscount = $platformVoucher->discount_amount ?? 0;
        $shopDiscount = $shopVoucher->discount_amount ?? 0;
        $shippingDiscount = $shippingVoucher->discount_amount ?? 0;

        // Chuẩn bị dữ liệu
        $totalWeight = 0;
        $maxLength = 0;
        $maxWidth = 0;
        $maxHeight = 0;
        $itemsResult = [];

        foreach ($items as $item) {
            $detail = $order->orderDetails->firstWhere('id', $item['order_detail_id']);
            if (!$detail)
                continue;

            // Lấy số lượng người dùng yêu cầu hoàn (không phải qty gốc trong orderDetail)
            $qty = $detail->quantity;
            $price = $detail->productVariant->price;
            $subtotal = $qty * $price;

            $product = $detail->productVariant->product;

            // Tính khối lượng + kích thước
            $totalWeight += $product->weight * $qty;
            $maxLength = max($maxLength, (float) $product->length);
            $maxWidth = max($maxWidth, (float) $product->width);
            $maxHeight = max($maxHeight, (float) $product->height);

            $itemsResult[] = [
                'order_detail_id' => $item['order_detail_id'],
                'variant_id' => $detail->productVariant->id,
                'qty' => $qty,
                'price_original' => $price,
                'subtotal' => $subtotal,
                'reason_type' => $item['reason_type'],
                'weight_item' => $product->weight * $qty,
            ];
        }

        // Tính phí ship trả hàng
        $address_pickup = $order->address;
        $shop = $order->shop;

        $calculateShippingFee = $this->ghn_service->calculateShippingFee(
            $address_pickup->district_id,
            $shop->district_id,
            $address_pickup->ward_code,
            $shop->ward_code,
            $totalWeight,
            $maxLength,
            $maxWidth,
            $maxHeight
        );

        $shipping_return_fee = $calculateShippingFee['data']['total'] ?? 0;

        // Bắt đầu phân bổ
        $totalRefund = 0;
        foreach ($itemsResult as &$item) {
            $ratio = $orderTotal > 0 ? $item['subtotal'] / $orderTotal : 0;

            // Phân bổ voucher & phí ship
            $platformDiscountItem = round($platformDiscount * $ratio);
            $shopDiscountItem = round($shopDiscount * $ratio);
            $shippingDiscountItem = round($shippingDiscount * $ratio);

            $weightRatio = $totalWeight > 0 ? $item['weight_item'] / $totalWeight : 0;
            $shippingFeeItem = round($shipping_return_fee * $weightRatio);

            // Ai chịu phí ship hoàn
            $shippingPayer = 'seller';
            $shippingReturnFeeItem = 0;

            if ($item['reason_type'] == 'buyer_fault' && $shipping_return_fee > 0) {
                $shippingReturnFeeItem = round($shipping_return_fee * $weightRatio);
                $shippingPayer = 'buyer';
            }

            if ($item['reason_type'] == 'shipping_faul' && $shipping_return_fee > 0) {
                $shippingPayer = 'platform';
            }

            // Tính refund_amount
            $refundAmount = $item['subtotal']
                - $platformDiscountItem
                - $shopDiscountItem
                + $shippingFeeItem
                - $shippingDiscountItem
                - $shippingReturnFeeItem;

            // Gán dữ liệu chuẩn với bảng refund_items
            $item['discount_allocated'] = $platformDiscountItem + $shopDiscountItem;
            $item['refund_amount'] = max($refundAmount, 0);
            $item['shipping_fee'] = $shippingFeeItem;
            $item['shipping_payer'] = $shippingPayer;

            unset($item['weight_item']); // bỏ trường phụ tính toán

            $totalRefund += $item['refund_amount'];
        }
        return [
            'refund_items' => $itemsResult,
            'total_refund' => $totalRefund,
            'shipping_return_fee' => $shipping_return_fee
        ];
    }
}