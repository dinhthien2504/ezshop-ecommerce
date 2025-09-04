<?php

namespace App\Services;

use App\Repositories\OrderEloquentRepository;
use App\Repositories\RefundEloquentRepository;
use App\Repositories\RefundItemEloquentRepository;
use App\Repositories\WalletEloquentRepository;
use App\Repositories\WalletTransactionEloquentRepository;
use App\Services\WalletService;

class RefundService
{
    protected $refund_rep;
    protected $refund_item_rep;
    protected $order_rep;
    protected $wallet_rep;
    protected $wallet_service;
    protected $wallet_transaction_rep;
    protected $notification_service;

    public function __construct(
        RefundEloquentRepository $refund_rep,
        RefundItemEloquentRepository $refund_item_rep,
        OrderEloquentRepository $order_rep,
        WalletEloquentRepository $wallet_rep,
        WalletTransactionEloquentRepository $wallet_transaction_rep,
        WalletService $wallet_service,
        NotificationService $notification_service
    ) {
        $this->refund_rep = $refund_rep;
        $this->refund_item_rep = $refund_item_rep;
        $this->order_rep = $order_rep;
        $this->wallet_rep = $wallet_rep;
        $this->wallet_service = $wallet_service;
        $this->wallet_transaction_rep = $wallet_transaction_rep;
        $this->notification_service = $notification_service;
    }

    public function createRefund($request)
    {
        $data = $request->all();
        $user_id = auth()->user()->id;
        // Tạo bản ghi refund
        $refund = $this->refund_rep->createRefund([
            'order_id' => $data['order_id'],
            'user_id' => $user_id,
            'address_id' => $data['address_id'],
            'total_refund' => $data['total_refund'],
            'status' => 'pending'
        ]);

        // 2. Chuẩn bị dữ liệu refund_items
        $items = [];
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $imageUrls = [];

                if (!empty($item['evidences'])) {
                    foreach ($item['evidences'] as $base64) {
                        $path = $this->saveBase64Image($base64);
                        if ($path) {
                            $imageUrls[] = $path;
                        }
                    }
                }

                $items[] = [
                    'refund_id' => $refund->id,
                    'order_detail_id' => $item['order_detail_id'],
                    'variant_id' => $item['variant_id'],
                    'qty' => $item['qty'],
                    'price_original' => $item['price_original'] ?? 0,
                    'subtotal' => $item['subtotal'] ?? 0,
                    'discount_allocated' => $item['discount_allocated'] ?? 0,
                    'refund_amount' => $item['refund_amount'] ?? 0,
                    'reason_type' => $item['reason_type'] ?? 'other',
                    'reason_detail' => $item['reason_detail'] ?? null,
                    'evidences' => !empty($imageUrls) ? json_encode($imageUrls) : null,
                    'shipping_fee' => $item['shipping_fee'] ?? 0,
                    'shipping_payer' => $item['shipping_payer'] ?? 'seller',
                    'updated_at' => now(),
                    'created_at' => now()
                ];
            }
        }
        if (!empty($items)) {
            $this->refund_item_rep->insertMultiple($items);
        }
        // Lấy ra đơn hàng để cập nhật trạng thái
        $order = $this->order_rep->find($data['order_id']);
        if (!$order) {
            throw new \Exception("Order Not Found", 404);
        }
        $order->load(['shop.user']);
        $order->order_status = 7;
        $order->save();
        $refund->load('refund_items');
        $this->notification_service->createNotification([
            'title' => 'Có yêu cầu hoàn trả mới',
            'message' => "Khách hàng đã gửi yêu cầu hoàn trả cho đơn hàng #{$refund->id}. Vui lòng xem chi tiết và phản hồi kịp thời.",
            'send_type' => 'to_shop',
            'receiver_ids' => [$order->shop->owner_id]
        ]);
        return $refund;
    }

    public function updateRefund($request, $id)
    {
        $status = $request->input('status');
        $refund = $this->refund_rep->find($id);
        if (!$refund) {
            throw new \Exception("Refund Not Found", 404);
        }
        //Tiến hành cập nhật lại trạng thái đơn hoàn trả
        $refund->status = $status;
        $refund->save();

        //Tiến hành xử lý các trường hợp
        switch ($status) {
            //Nếu do shop sai nhưng từ chối duyệt hoàn trả
            case 'approved':
                $this->notification_service->createNotification([
                    'title' => 'Yêu cầu hoàn trả đã được chấp nhận',
                    'message' => "Shop đã xác nhận yêu cầu hoàn trả của bạn cho đơn #{$refund->id}. Chúng tôi sẽ tiến hành xử lý theo quy trình.",
                    'send_type' => 'to_user',
                    'receiver_ids' => [$refund->user_id]
                ]);
                break;
            case 'rejected_by_shop':
                $this->notification_service->createNotification([
                    'title' => 'Yêu cầu hoàn trả bị từ chối',
                    'message' => "Shop đã từ chối yêu cầu hoàn trả của bạn cho đơn #{$refund->id}. Nếu bạn không đồng ý với quyết định này, bạn có thể khiếu nại lên CSKH để được hỗ trợ.",
                    'send_type' => 'to_user',
                    'receiver_ids' => [$refund->user_id],
                    'link' => env('FRONTEND_URL') . '/nguoi-dung/don-hang-' . $refund->order_id
                ]);
                break;
            //Nếu do khách hàng sai thì không hoàn trả và cập nhật dữ liệu bán hàng
            case 'escalated':
                $this->notification_service->createNotification([
                    'title' => 'Người dùng khiếu nại lên CSKH',
                    'message' => "Người dùng #{$refund->user_id} đã khiếu nại đơn hoàn trả #{$refund->id} cần xử lý",
                    'send_type' => 'to_admin',
                    'link' => env('FRONTEND_URL') . '/admin/don-hang?search_code_order=' . $refund->order_id
                ]);
                break;
            case 'rejected_final':
                $this->rejectFinalRefund($refund);
                break;
            //Đơn hàng hoàn thành xử lý dữ liệu cho sản phẩm và thanh toán 
            case 'processed':
                $this->processRefund($refund);
                break;
        }
        return $refund->status;
    }

    private function processRefund($refund)
    {
        $refund->load([
            'refund_items',
            'order.orderDetails.productVariant',
            'order.shop'
        ]);

        $order = $refund->order;
        if (!$order) {
            throw new \Exception("Order not found for refund", 500);
        }

        // Lấy danh sách item hoàn trả (theo order_detail_id)
        $refundItemIds = $refund->refund_items->pluck('order_detail_id')->toArray();

        $totalRefundToBuyer = (float) $refund->total_refund;
        $sellerShipTotal = (float) $refund->refund_items
            ->where('shipping_payer', 'seller')
            ->sum('shipping_fee');

        // ví sàn
        $platformWallet = $this->wallet_rep->getWalletPlatform();
        if (!$platformWallet) {
            throw new \Exception("Ví sàn không tồn tại", 500);
        }

        // ví buyer
        $buyerWallet = $this->wallet_rep->getWalletByUser($refund->user_id);
        if (!$buyerWallet) {
            $buyerWallet = $this->wallet_rep->create([
                'user_id' => $refund->user_id,
                'type' => 'user',
                'balance' => 0,
            ]);
        }

        // ví seller (nếu cần trừ ship)
        $sellerWallet = null;
        $sellerUserId = optional($order->shop)->owner_id;
        if ($sellerShipTotal > 0) {
            if (!$sellerUserId) {
                throw new \Exception("Không tìm thấy chủ shop để trừ phí ship", 500);
            }
            $sellerWallet = $this->wallet_rep->getWalletByUser($sellerUserId);
            if (!$sellerWallet) {
                $sellerWallet = $this->wallet_rep->create([
                    'user_id' => $sellerUserId,
                    'type' => 'user',
                    'balance' => 0,
                ]);
            }
        }

        // ---- 2) Xử lý giao dịch bằng WalletService ----
        // Sàn trả tiền cho user
        $this->wallet_service->updateWalletBalance(
            $platformWallet->id,
            $totalRefundToBuyer,
            'refund',
            $order->id,
            "Hoàn tiền buyer refund #{$refund->id}"
        );

        $this->wallet_service->updateWalletBalance(
            $buyerWallet->id,
            $totalRefundToBuyer,
            'deposit',
            $order->id,
            "Nhận hoàn tiền refund #{$refund->id}"
        );

        // Sàn trả tiền cho shop nếu có
        $amountToSeller = $this->calculateRefund($order, $refundItemIds) ?? 0;

        if ($amountToSeller > 0 && $sellerWallet) {
            if ($sellerWallet->debit > 0 && $sellerWallet->debit < $amountToSeller) {
                $amountToSeller -= $sellerWallet->debit;
            }
            $sellerWallet->balance += $amountToSeller;
            // Seller nhận tiền
            $this->wallet_service->updateWalletBalance(
                $sellerWallet->id,
                $amountToSeller,
                'order_release_seller',
                $order->id,
                "Shop #{$order->shop_id} nhận tiền còn lại từ đơn hoàn #{$refund->id}"
            );

            // Platform chi tiền
            $this->wallet_service->updateWalletBalance(
                $platformWallet->id,
                $amountToSeller,
                'order_release_platform',
                $order->id,
                "Platform chi trả cho shop #{$order->shop_id} từ đơn hoàn #{$refund->id}"
            );
        }

        // Shop chịu phí ship thì trừ vào shop, cộng lại vào sàn
        if ($sellerShipTotal > 0 && $sellerWallet) {
            if ($sellerWallet->balance < $sellerShipTotal) {
                $sellerWallet->debit += $sellerShipTotal;
                $sellerWallet->save();

                $this->wallet_transaction_rep->createTransaction(
                    $sellerWallet->id,
                    $sellerShipTotal,
                    'debit',
                    $sellerWallet->balance,
                    "Ghi nợ phí ship refund #{$refund->id} do shop không đủ số dư"
                );
            } else {
                $this->wallet_service->updateWalletBalance(
                    $sellerWallet->id,
                    $sellerShipTotal,
                    'withdraw',
                    $order->id,
                    "Trừ phí ship hoàn trả refund #{$refund->id}"
                );

                $this->wallet_service->updateWalletBalance(
                    $platformWallet->id,
                    $sellerShipTotal,
                    'deposit',
                    $order->id,
                    "Thu hồi phí ship từ shop refund #{$refund->id}"
                );
            }
        }
        // Cập nhật lại trạng thái thanh toán bên order
        $order->payment_status = 'refunded';
        $order->save();

        // ---- Update tồn kho / lượt bán ----
        foreach ($order->orderDetails as $detail) {
            $variant = $detail->productVariant;
            if (!$variant)
                continue;

            if (in_array($detail->id, $refundItemIds)) {
                $refundItem = $refund->refund_items->firstWhere('order_detail_id', $detail->id);
                if ($refundItem) {
                    $variant->stock += $refundItem->qty;
                    $variant->save();
                }
            } else {
                $variant->sell += $detail->quantity;
                $variant->save();
            }
        }
    }

    public function rejectFinalRefund($refund)
    {
        $refund->load([
            'refund_items',
            'order.orderDetails.productVariant',
            'order.shop'
        ]);

        $order = $refund->order;
        if (!$order) {
            throw new \Exception("Order not found for refund", 500);
        }

        // Ví sàn
        $platformWallet = $this->wallet_rep->getWalletPlatform();
        if (!$platformWallet) {
            throw new \Exception("Ví sàn không tồn tại", 500);
        }

        // Ví seller
        $sellerUserId = optional($order->shop)->owner_id;
        if (!$sellerUserId) {
            throw new \Exception("Không tìm thấy chủ shop", 500);
        }

        $sellerWallet = $this->wallet_rep->getWalletByUser($sellerUserId);
        if (!$sellerWallet) {
            $sellerWallet = $this->wallet_rep->create([
                'user_id' => $sellerUserId,
                'type' => 'user',
                'balance' => 0,
            ]);
        }

        // --- 1) Sàn thanh toán cho shop ---
        $amountToSeller = $order->amount_to_seller ?? 0;

        if ($amountToSeller > 0) {
            $amountRemaining = $amountToSeller;

            // Nếu shop có nợ debit thì trừ trước
            if ($sellerWallet->debit > 0) {
                if ($amountRemaining >= $sellerWallet->debit) {
                    // đủ tiền để trừ hết nợ
                    $amountRemaining -= $sellerWallet->debit;
                    $sellerWallet->debit = 0;
                } else {
                    // chỉ trừ được một phần nợ
                    $sellerWallet->debit -= $amountRemaining;
                    $amountRemaining = 0;
                }
                $sellerWallet->save();
            }

            // Nếu còn dư thì cộng vào balance
            if ($amountRemaining > 0) {
                $this->wallet_service->updateWalletBalance(
                    $sellerWallet->id,
                    $amountRemaining,
                    'order_release_seller',
                    $order->id,
                    "Shop #{$order->shop_id} nhận tiền từ đơn hoàn #{$refund->id}"
                );
            }

            // Platform chi tiền (toàn bộ amountToSeller)
            $this->wallet_service->updateWalletBalance(
                $platformWallet->id,
                $amountToSeller,
                'order_release_platform',
                $order->id,
                "Platform chi trả cho shop #{$order->shop_id} từ đơn hoàn #{$refund->id}"
            );
        }

        // --- 2) Cập nhật lượt bán sản phẩm ---
        foreach ($order->orderDetails as $detail) {
            $variant = $detail->productVariant;
            if ($variant) {
                $variant->sell += $detail->quantity;
                $variant->save();
            }
        }
        $this->notification_service->createNotification([
            'title' => 'Yêu cầu hoàn trả bị từ chối',
            'message' => "CSKH đã từ chối yêu cầu hoàn trả của bạn cho đơn #{$refund->id}. 
                  Nếu bạn không đồng ý với quyết định này, vui lòng liên hệ lại bộ phận CSKH để được hỗ trợ thêm.",
            'send_type' => 'to_user',
            'receiver_ids' => [$refund->user_id],
            'link' => env('FRONTEND_URL') . '/nguoi-dung/don-hang-' . $refund->order_id
        ]);
    }
    private function saveBase64Image(string $base64): ?string
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1]);
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }

        $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
        if ($imageData === false) {
            return null;
        }

        $filename = time() . '_' . uniqid() . '.' . $extension;

        $targetDir = base_path("../frontend/public/imgs/refunds");

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fullPath = "$targetDir/$filename";
        file_put_contents($fullPath, $imageData);
        return $filename;
    }

    private function calculateRefund($order, $refundItemIds)
    {
        $productTotal = 0;
        $refundProductTotal = 0;
        foreach ($order->orderDetails as $detail) {
            $proTotal = $detail->quantity * $detail->productVariant->price;
            $productTotal += $proTotal;

            if (in_array($detail->id, $refundItemIds)) {
                $refundProductTotal += $proTotal;
            }
        }
        if ($refundProductTotal <= 0) {
            return $order->amount_to_seller;
        }

        $sellerRefundAmount = round(
            $order->amount_to_seller * ($refundProductTotal / $productTotal),
            0
        );

        $amountToSeller = $order->amount_to_seller - $sellerRefundAmount;

        return $amountToSeller;
    }
}