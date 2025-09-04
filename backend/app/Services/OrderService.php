<?php

namespace App\Services;

use App\Mail\OrderCreatedMail;
use App\Models\ProductVariant;
use App\Repositories\CartItemEloquentRepository;
use App\Repositories\OrderDetailEloquentRepository;
use App\Repositories\OrderEloquentRepository;
use App\Repositories\WalletEloquentRepository;
use App\Repositories\WalletTransactionEloquentRepository;
use App\Repositories\VoucherEloquentRepository;
use App\Services\GhnLocationService;
use App\Services\GhnService;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;

class OrderService
{
    protected $orderRepository;
    protected $orderAppliedVoucherService;
    protected $orderDetailRepository;
    protected $cartItemRepository;
    protected $voucherRepository;
    protected $gnhLocatitonService;
    protected $ghnService;
    protected $walletRepository;
    protected $walletTransactionRepository;
    protected $walletService;
    protected $notification_service;
    public function __construct(
        OrderEloquentRepository $orderRepository,
        OrderDetailEloquentRepository $orderDetailRepository,
        OrderAppliedVoucherService $orderAppliedVoucherService,
        CartItemEloquentRepository $cartItemRepository,
        VoucherEloquentRepository $voucherRepository,
        GhnLocationService $gnhLocatitonService,
        GhnService $ghnService,
        WalletEloquentRepository $walletRepository,
        WalletTransactionEloquentRepository $walletTransactionRepository,
        WalletService $walletService,
        NotificationService $notification_service
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderAppliedVoucherService = $orderAppliedVoucherService;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->voucherRepository = $voucherRepository;
        $this->gnhLocatitonService = $gnhLocatitonService;
        $this->ghnService = $ghnService;
        $this->walletRepository = $walletRepository;
        $this->walletTransactionRepository = $walletTransactionRepository;
        $this->walletService = $walletService;
        $this->notification_service = $notification_service;
    }

    public function getOrders($filters)
    {
        // Lấy dữ liệu đã phân trang
        $ordersPaginator = $this->orderRepository->getOrders($filters);

        // Map dữ liệu từng order
        $orders = $ordersPaginator->getCollection()->map(function ($order) {
            $addressInfo = $order->address;

            // Địa chỉ khách hàng đầy đủ
            $customerAddress = $this->gnhLocatitonService->getFullAddress(
                $addressInfo->address_detail,
                $addressInfo->district_id,
                $addressInfo->ward_code
            );
            return [
                'order_id' => $order->id,
                'shop_id' => $order->shop_id,
                'total_amount' => $order->total_amount,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'created_date' => $order->created_date,
                // Thông tin cửa hàng
                'shop_name' => $order->shop->shop_name,
                'shop_phone' => $order->shop->phone,
                'shop_address' => $order->shop->address,
                'user' => $order->user,
                'shop' => $order->shop,
                // Thông tin khách hàng
                'customer_name' => $addressInfo->name,
                'customer_phone' => $addressInfo->phone,
                'customer_address' => $customerAddress,

                // Thanh toán & phí
                'payment_method' => $order->paymentMethod->title ?? null,
                'shipping_fee' => $order->shipping_fee,
                'shipping_discount' => $shippingVoucher->discount_amount ?? 0,
                'platform_discount' => $platformVoucher->discount_amount ?? 0,
                'shop_discount' => $shopVoucher->discount_amount ?? 0,

                // Trả hàng hoàn tiền nếu có
                'refund' => $order->refund,
                'order_details' => $order->orderDetails->map(function ($detail) use ($order) {
                    $variant = $detail->productVariant;
                    $refundItem = $detail->refundItems->first();
                    return [
                        'order_detail_id' => $detail->id,
                        'variant_id' => $detail->variant_id,
                        'quantity' => $detail->quantity,
                        'product_name' => $variant->product->name ?? null,
                        'sku' => $variant->sku ?? null,
                        'slug' => $variant->product->slug ?? null,
                        'product_id' => $variant->product->id,
                        'price' => $variant->price,
                        'image' => $variant->image ?? null,
                        'attributes' => VariantAttributeService::getFullNameVariant($variant->variantAttribute),

                        // Thêm thông tin refund
                        'refund_items' => $refundItem ? [
                            'refund_id' => $refundItem->refund_id,
                            'reason_type' => $refundItem->reason_type,
                            'reason_detail' => $refundItem->reason_detail,
                            'subtotal' => $refundItem->subtotal,
                            'qty' => $refundItem->qty,
                            'discount_allocated' => $refundItem->discount_allocated,
                            'evidences' => $refundItem->evidences ? json_decode($refundItem->evidences, true) : [],
                            'refund_amount' => $refundItem->refund_amount,
                            'shipping_fee' => $refundItem->shipping_fee,
                            'shipping_payer' => $refundItem->shipping_payer,
                            'status' => $order->refund->status ?? null,
                        ] : null
                    ];
                })
            ];
        });

        return [
            'orders' => $orders,
            'meta' => [
                'current_page' => $ordersPaginator->currentPage(),
                'last_page' => $ordersPaginator->lastPage(),
                'per_page' => $ordersPaginator->perPage(),
                'total' => $ordersPaginator->total(),
            ],
        ];
    }

    public function createOrders(
        array $ordersData,
        array $vouchers,
        array $cartIds,
        $address_id,
        $user_id,
        int $payment_method_id,
        $payment_status = 'unpaid',
        $order_status = 1
    ) {
        DB::beginTransaction();
        try {
            $orderDetailsBulk = [];

            foreach ($ordersData as $orderData) {
                $totalVatAmount = 0;
                $totalPlatformFee = 0;
                $totalTaxAmount = 0;

                foreach ($orderData['variants'] as $variant) {
                    $variantModel = ProductVariant::with(['product.category.tax', 'product.category.fee'])
                        ->find($variant['variant_id']);

                    if (!$variantModel)
                        continue;

                    $price = $variantModel->price; // đã bao gồm VAT
                    $quantity = $variant['quantity'];
                    $v = $variantModel->product->category;
                    $vatPercent = $v->tax->vat_percent ?? 0;
                    $taxPercent = $v->tax->tax_percent ?? 0;
                    $platformPercent = $v->fee->percentage ?? 0;

                    // Thành tiền có VAT
                    $lineTotalWithVat = $price * $quantity;
                    // Bóc VAT ra
                    $lineTotalExclVat = $lineTotalWithVat / (1 + $vatPercent / 100);

                    // VAT
                    $lineVat = $lineTotalWithVat - $lineTotalExclVat;
                    $totalVatAmount += $lineVat;

                    // Phí sàn (tính trên giá chưa VAT)
                    $totalPlatformFee += $lineTotalExclVat * ($platformPercent / 100);

                    // Thuế TNCN (tính trên giá chưa VAT)
                    $totalTaxAmount += $lineTotalExclVat * ($taxPercent / 100);

                    // Trừ tồn kho
                    $variantModel->stock -= $quantity;
                    $variantModel->save();
                }

                // Tổng người mua trả sản phẩm trả cho người bán(chưa - thuế phí)
                $amountInclVat = $orderData['amount_to_seller'];

                // Doanh thu chưa VAT
                $amountExclVat = $amountInclVat - $totalVatAmount;

                // Số tiền thực seller nhận
                $amountToSeller = $amountExclVat - $totalPlatformFee - $totalTaxAmount;
                $orderPayload = [
                    'user_id' => $user_id,
                    'shop_id' => $orderData['shop_id'],
                    'payment_method_id' => $payment_method_id,
                    'address_id' => $address_id,
                    'total_amount' => $orderData['total_amount'],
                    'amount_to_seller' => $amountToSeller,
                    'total_vat_amount' => round($totalVatAmount, 2),
                    'platform_fee_amount' => round($totalPlatformFee, 2),
                    'tax_amount' => round($totalTaxAmount, 2),
                    'order_status' => $order_status,
                    'payment_status' => $payment_status,
                    'shipping_fee' => $orderData['shipping_fee'],
                    'created_date' => now(),
                ];
                $order = $this->orderRepository->create($orderPayload);
                //Nếu đơn hàng thanh toán online thì lập tức cộng tiền vào ví sàn
                if ($payment_method_id != 1 && $payment_status == 'paid') {
                    $wallet_platform = $this->walletRepository->getWalletPlatform();
                    if ($wallet_platform) {
                        $this->walletService->updateWalletBalance($wallet_platform->id, $orderData['total_amount'], 'order_payment', $order->id);
                    }
                }
                $this->orderAppliedVoucherService->addVoucherToOrder($vouchers, $order->id, $orderData['shop_id']);
                foreach ($orderData['variants'] as $variant) {
                    $orderDetailsBulk[] = [
                        'order_id' => $order->id,
                        'variant_id' => $variant['variant_id'],
                        'quantity' => $variant['quantity'],
                        'is_reviewed' => false,
                        'created_at' => now()
                    ];
                }
                $order->load([
                    'user',
                    'shop.user'
                ]);
                $this->notification_service->createNotification([
                    'title' => 'Có đơn hàng mới!',
                    'message' => "Bạn vừa nhận được đơn hàng #{$order->id} từ khách hàng {$order->user->user_name}. Vui lòng xác nhận và chuẩn bị giao hàng.",
                    'send_type' => 'to_shop',
                    'receiver_ids' => [$order->shop->owner_id],
                    'link' => env('FRONTEND_URL') . '/kenh-nguoi-ban/don-hang'
                ]);
            }
            $voucherIds = array_column($vouchers, 'id');
            $this->voucherRepository->decrementVoucherQuantities($voucherIds);
            $this->orderDetailRepository->insertMultiple($orderDetailsBulk);
            $this->cartItemRepository->deleteByIds($cartIds);
            // \Mail::to($order->user->email)->send(new OrderCreatedMail($order));
            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return false;
        }
    }

    public function orderByUser()
    {
        $user = auth()->user();
        $orders = $this->orderRepository->getOrdersByUserId($user->id);
        $orders = $orders->map(function ($order) {
            $address_pickup = $order->address;
            $shop = $order->shop;
            return [
                'order_id' => $order->id,
                'shop_id' => $order->shop_id,
                'shop_name' => $shop->shop_name,
                'total_amount' => $order->total_amount,
                'order_status' => $order->order_status,
                'address_id' => $order->address_id,
                'shop' => $order->shop,
                'pickup_address' => $this->gnhLocatitonService->getFullAddress($address_pickup->address_detail, $address_pickup->district_id, $address_pickup->ward_code),
                'shipping_fee' => $order->shipping_fee,
                'order_details' => $order->orderDetails->map(function ($detail) {
                    $variant = $detail->productVariant;
                    return [
                        'order_detail_id' => $detail->id,
                        'variant_id' => $detail->variant_id,
                        'quantity' => $detail->quantity,
                        'product_name' => $variant->product->name ?? null,
                        'price' => $variant->price,
                        'image' => $variant->image ?? null,
                        'is_reviewed' => $detail->is_reviewed,
                        'attributes' => VariantAttributeService::getFullNameVariant(variantAttributes: $detail->productVariant->variantAttribute)
                    ];
                })
            ];
        });
        return $orders;
    }

    public function orderByUserOrderId($order_id)
    {
        $user = auth()->user();
        $order = $this->orderRepository->getOrderByUserOrderId($order_id, $user->id);

        $addressInfo = $order->address;
        $addressDetail = $addressInfo->address_detail;
        $districtId = $addressInfo->district_id;
        $wardCode = $addressInfo->ward_code;

        $customerAddress = $this->gnhLocatitonService->getFullAddress(
            $addressDetail,
            $districtId,
            $wardCode
        );
        $shippingVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shipping');
        $platformVoucher = $order->orderAppliedVoucher->firstWhere('type', 'platform');
        $shopVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shop');
        $productTotal = $order->orderDetails->sum(function ($detail) {
            $variant = $detail->productVariant;
            return $detail->quantity * $variant->price;
        });
        return [
            'order_id' => $order->id,
            'refund_id' => $order->refund->id ?? null,
            'shop_id' => $order->shop_id,
            'shop_name' => $order->shop->shop_name,
            'total_amount' => $order->total_amount,
            'product_total' => $productTotal,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'created_date' => $order->created_date,
            'customer_name' => $addressInfo->name,
            'customer_phone' => $addressInfo->phone,
            'customer_address' => $customerAddress,
            'payment_method' => $order->paymentMethod->title,
            'shipping_discount' => $shippingVoucher->discount_amount ?? 0,
            'shipping_fee' => $order->shipping_fee,
            'platform_discount' => $platformVoucher->discount_amount ?? 0,
            'shop_discount' => $shopVoucher->discount_amount ?? 0,
            'total_refund' => $order->refund->total_refund ?? 0,
            'refund_status' => $order->refund->status ?? null,
            'order_details' => $order->orderDetails->map(function ($detail) use ($order) {
                $variant = $detail->productVariant;
                $refundItem = $detail->refundItems->first();
                return [
                    'product_id' => $variant->product->id,
                    'slug' => $variant->product->slug,
                    'variant_id' => $detail->variant_id,
                    'quantity' => $detail->quantity,
                    'product_name' => $variant->product->name,
                    'price' => $variant->price,
                    'image' => $variant->image,
                    'attributes' => VariantAttributeService::getFullNameVariant($variant->variantAttribute),

                    // Thêm thông tin refund
                    'refund' => $refundItem ? [
                        'refund_id' => $refundItem->refund_id,
                        'refund_amount' => $refundItem->refund_amount,
                        'shipping_fee' => $refundItem->shipping_fee,
                        'status' => $order->refund->status,
                    ] : null
                ];
            }),
        ];
    }

    public function changeStatus($request, $id)
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw new \Exception("Đơn hàng không tồn tại", 404);
        }

        $order->load([
            'orderDetails.productVariant',
            'shop'
        ]);

        $request_status = $request->input('status');
        $order->order_status = $request_status;

        if ($request_status == 4) {
            $order->payment_status = 'paid';
            // --- Xử lý ví sàn ---
            $walletPlatform = $this->walletRepository->getWalletPlatform();
            if (!$walletPlatform) {
                throw new \Exception("Ví sàn không tồn tại", 500);
            }
            $platformIncome = $order->total_amount;
            $walletPlatform->balance += $platformIncome;
            $walletPlatform->save();

            $this->walletTransactionRepository->create([
                'wallet_id' => $walletPlatform->id,
                'order_id' => $order->id,
                'type' => 'order_payment',
                'amount' => $platformIncome,
                'balance_after' => $walletPlatform->balance,
                'note' => "Thu từ đơn hàng #{$order->id}",
                'created_at' => now(),
            ]);
        } elseif ($request_status == 5) {
            foreach ($order->orderDetails as $detail) {
                $variant = $detail->productVariant;
                if ($variant) {
                    $variant->sell += $detail->quantity;
                    $variant->save();
                }
            }
            // --- Xử lý ví người bán (shop) ---
            $sellerUserId = $order->shop->owner_id; // Lấy user_id của chủ shop
            $sellerWallet = $this->walletRepository->getWalletByUser($sellerUserId);
            if (!$sellerWallet) {
                // Nếu chưa có ví thì tạo mới
                $sellerWallet = $this->walletRepository->create([
                    'user_id' => $sellerUserId,
                    'type' => 'user',
                    'balance' => 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $amountToSeller = $order->amount_to_seller;

            $this->walletService->updateWalletBalance(
                $sellerWallet->id,
                $amountToSeller,
                'order_release_seller',
                $order->id,
                "Nhận tiền bán hàng từ đơn #{$order->id}"
            );
            $this->notification_service->createNotification([
                'title' => 'Đơn hàng #' . $order->id . ' đã hoàn tất',
                'message' => "Đơn hàng #{$order->id} đã được giao thành công. Số tiền bạn sẽ nhận được là "
                    . number_format($order->amount_to_seller, 0, ',', '.') . " VNĐ. Vui lòng kiểm tra ví của bạn.",
                'send_type' => 'to_shop',
                'receiver_ids' => [$order->shop->owner_id],
                'link' => env('FRONTEND_URL') . '/kenh-nguoi-ban/tai-chinh'
            ]);
        } elseif ($request_status == 6) {
            foreach ($order->orderDetails as $detail) {
                $variant = $detail->productVariant;
                if ($variant) {
                    $variant->stock += $detail->quantity;
                    $variant->save();
                }
            }
            $this->notification_service->createNotification([
                'title' => 'Đơn hàng #' . $order->id . ' đã bị hủy',
                'message' => "Đơn hàng #{$order->id} đã bị hủy. Sản phẩm trong đơn đã được hoàn lại vào tồn kho.",
                'send_type' => 'to_shop',
                'receiver_ids' => [$order->shop->owner_id],
                'link' => env('FRONTEND_URL') . '/kenh-nguoi-ban/don-hang'
            ]);
        }

        $order->save();
        return $order;
    }

    public function getShopOrders($request)
    {
        $shop_id = auth()->user()->shop->id;
        $order_status = $request->input('order_status');

        $orders = $this->orderRepository->getOrdersByShopAndStatus($shop_id, $order_status);

        $orders = $orders->map(function ($order) {
            $addressInfo = $order->address;

            // Địa chỉ khách hàng đầy đủ
            $customerAddress = $this->gnhLocatitonService->getFullAddress(
                $addressInfo->address_detail,
                $addressInfo->district_id,
                $addressInfo->ward_code
            );

            $shippingVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shipping');
            $platformVoucher = $order->orderAppliedVoucher->firstWhere('type', 'platform');
            $shopVoucher = $order->orderAppliedVoucher->firstWhere('type', 'shop');

            $productTotal = $order->orderDetails->sum(function ($detail) {
                return $detail->quantity * ($detail->productVariant->price ?? 0);
            });

            return [
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'product_total' => $productTotal,
                'payment_status' => $order->payment_status,
                'order_status' => $order->order_status,
                'created_date' => $order->created_date,
                // Thông tin cửa hàng
                'shop_name' => $order->shop->shop_name,
                'shop_phone' => $order->shop->phone,
                'shop_address' => $order->shop->address,

                // Thông tin khách hàng
                'customer_name' => $addressInfo->name,
                'customer_phone' => $addressInfo->phone,
                'customer_address' => $customerAddress,

                // Thanh toán & phí
                'payment_method' => $order->paymentMethod->title ?? null,
                'shipping_fee' => $order->shipping_fee,
                'shipping_discount' => $shippingVoucher->discount_amount ?? 0,
                'platform_discount' => $platformVoucher->discount_amount ?? 0,
                'shop_discount' => $shopVoucher->discount_amount ?? 0,

                // Trả hàng hoàn tiền nếu có
                'refund' => $order->refund,
                // Chi tiết sản phẩm trong đơn
                'order_details' => $order->orderDetails->map(function ($detail) use ($order) {
                    $variant = $detail->productVariant;
                    $refundItem = $detail->refundItems->first();
                    return [
                        'order_detail_id' => $detail->id,
                        'variant_id' => $detail->variant_id,
                        'quantity' => $detail->quantity,
                        'product_name' => $variant->product->name ?? null,
                        'sku' => $variant->sku ?? null,
                        'product_id' => $variant->product->id ?? null,
                        'price' => $variant->price,
                        'image' => $variant->image ?? null,
                        'attributes' => VariantAttributeService::getFullNameVariant($variant->variantAttribute),

                        // Thêm thông tin refund
                        'refund_items' => $refundItem ? [
                            'refund_id' => $refundItem->refund_id,
                            'reason_type' => $refundItem->reason_type,
                            'reason_detail' => $refundItem->reason_detail,
                            'subtotal' => $refundItem->subtotal,
                            'qty' => $refundItem->qty,
                            'discount_allocated' => $refundItem->discount_allocated,
                            'evidences' => $refundItem->evidences ? json_decode($refundItem->evidences, true) : [],
                            'refund_amount' => $refundItem->refund_amount,
                            'shipping_fee' => $refundItem->shipping_fee,
                            'shipping_payer' => $refundItem->shipping_payer,
                            'status' => $order->refund->status ?? null,
                        ] : null
                    ];
                }),
            ];
        });

        return $orders;
    }

    public function autoCompleteDeliveredOrders()
    {
        $orders = $this->orderRepository->getDeliveredOrdersOverDays(7);

        foreach ($orders as $order) {
            $fakeRequest = new \Illuminate\Http\Request([
                'status' => 5
            ]);

            $this->changeStatus($fakeRequest, $order->id);
        }

        return count($orders);
    }
}