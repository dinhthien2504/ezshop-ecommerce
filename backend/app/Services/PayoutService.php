<?php

namespace App\Services;

use App\Http\Controllers\MockBankController;
use App\Repositories\OrderDetailEloquentRepository;
use App\Repositories\PayoutEloquentRepository;
use App\Repositories\WalletEloquentRepository;
use App\Repositories\WalletTransactionEloquentRepository;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\PayoutRejectedMail;
use Illuminate\Support\Facades\Mail;

class PayoutService
{
    protected $payout_rep;
    protected $order_detail_rep;
    protected $wallet_rep;
    protected $platformWalletTransactionRepository;
    protected $wallet_service;
    protected $notification_service;

    public function __construct(
        PayoutEloquentRepository $payout_rep,
        OrderDetailEloquentRepository $order_detail_rep,
        WalletEloquentRepository $wallet_rep,
        WalletTransactionEloquentRepository $walletTransactionRepository,
        WalletService $wallet_service,
        NotificationService $notification_service
    ) {
        $this->payout_rep = $payout_rep;
        $this->order_detail_rep = $order_detail_rep;
        $this->wallet_rep = $wallet_rep;
        $this->walletTransactionRepository = $walletTransactionRepository;
        $this->wallet_service = $wallet_service;
        $this->notification_service = $notification_service;
    }

    public function getPayouts($request)
    {
        $filters = [
            'search' => $request->input('search'),
            'per_page' => $request->input('per_page', 10),
            'approval_status' => $request->input('approval_status'),
            'payout_status' => $request->input('payout_status'),
        ];

        return $this->payout_rep->getFilteredPayouts($filters);
    }

    public function getPayoutsByShopId()
    {
        $shop = $this->getShopInfo();
        return $this->payout_rep->getPayoutsByShopId($shop->id);
    }

    public function requestPayout(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            if (!$user) {
                throw new \Exception("User not found", 404);
            }

            $shop = $this->getShopInfo();
            if (!$shop) {
                throw new \Exception("Shop not found", 404);
            }

            $wallet = $this->wallet_rep->getWalletByUser($user->id);
            if (!$wallet) {
                throw new \Exception("Wallet not found", 404);
            }

            $amountRequested = (float) $request->input('amount');
            if ($amountRequested <= 0 || $wallet->balance < $amountRequested) {
                throw new \Exception("Số tiền rút không hợp lệ");
            }

            // Trừ số dư ví
            $this->wallet_service->updateWalletBalance(
                $wallet->id,
                $amountRequested,
                'withdraw',
                null,
                'Yêu cầu rút tiền'
            );

            // Tạo payout record
            $payoutId = $this->payout_rep->createPayout([
                'shop_id' => $shop->id,
                'payout_code' => 'PO' . strtoupper(Str::random(10)),
                'net_amount' => $amountRequested,
                'transaction_code' => null,
                'payout_status' => 'pending',
                'requested_at' => now(),
                'processed_at' => null,
                'processed_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            $this->notification_service->createNotification([
                'title' => 'Có yêu cầu rút tiền mới',
                'message' => "Shop {$shop->shop_name} vừa tạo yêu cầu rút tiền. Vui lòng kiểm tra và xử lý.",
                'send_type' => 'to_admin',
                'receiver_ids' => null,
                'link' => env('FRONTEND_URL') . '/admin/lenh-rut-tien'
            ]);

            return [
                'payout_id' => $payoutId,
                'net_amount' => $amountRequested,
                'status' => 'pending',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            throw $e;
        }
    }

    private function getShopInfo()
    {
        $shop = auth()->user()->shop()->first();

        if (!$shop) {
            throw new \Exception("Shop không tồn tại.", 404);
        }
        return $shop;
    }

    public function processPayout($id)
    {
        $adminId = auth()->user()->id;
        $payout = $this->payout_rep->find($id);
        $payout->load('shop');

        if ($payout->approval_status === 'rejected') {
            throw new \Exception('Lệnh rút tiền đã bị từ chối, không thể xử lý.');
        }

        if ($payout->payout_status === 'completed') {
            throw new \Exception('Payout đã được chuyển thành công, không thể xử lý lại.');
        }

        $shop = $payout->shop;

        //Tạo payload gửi đến ngân hàng giả
        $payload = [
            'amount' => (int) $payout->net_amount,
            'account_number' => $shop->bank_account_number,
            'account_name' => $shop->bank_account_name,
            'bank_code' => $shop->bank_short_name,
            'bin' => $shop->bin_account,
            'note' => 'Thanh toán rút tiền: ' . $payout->payout_code,
        ];

        // Gửi đến API ngân hàng
        $response = $this->bankTransfer($payload);

        if ($response['success']) {
            // ✅ Cập nhật trạng thái payout
            $payout->update([
                'payout_status' => 'completed',
                'approval_status' => 'approved',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'transaction_code' => $response['transaction_code'],
            ]);

            // ✅ Lấy ví sàn
            $wallet_platform = $this->wallet_rep->getWalletPlatform();
            if (!$wallet_platform) {
                throw new \Exception("Ví sàn không tồn tại", 500);
            }

            // ✅ Dùng updateWalletBalance thay vì tự trừ
            $this->wallet_service->updateWalletBalance(
                $wallet_platform->id,
                $payout->net_amount,
                'withdraw',
                null,
                "Thanh toán rút tiền cho shop #{$payout->shop_id}"
            );

        } else {
            $payout->update([
                'payout_status' => 'failed',
                'processed_by' => $adminId,
                'processed_at' => now(),
                'frozen_amount' => $payout->net_amount,
            ]);
        }

        $payout->refresh();
        $payout->load('shop', 'processedByUser');
        $this->notification_service->createNotification([
            'title' => 'Rút tiền thành công',
            'message' => "Yêu cầu rút tiền #{$payout->payout_code} của bạn đã được xử lý thành công. Số tiền sẽ được chuyển về tài khoản ngân hàng đã đăng ký trong thời gian sớm nhất.",
            'send_type' => 'to_shop',
            'receiver_ids' => [$shop->owner_id],
            'link' => env('FRONTEND_URL') . '/kenh-nguoi-ban/tai-chinh'
        ]);

        return [
            'message' => 'Payout đã được duyệt và xử lý.',
            'payout' => $payout
        ];
    }

    private function bankTransfer($payload)
    {
        $mock = new MockBankController();

        // Gọi trực tiếp phương thức transfer của controller, truyền payload làm Request
        $response = $mock->transfer(new Request($payload));

        // Lấy dữ liệu từ response JSON
        $data = $response->getData(true);
        if ($data['success'] ?? false) {
            return [
                'success' => true,
                'transaction_code' => $data['transaction_code'],
            ];
        }

        return [
            'success' => false,
            'message' => $data['message'] ?? 'Lỗi không xác định',
        ];
    }

    public function rejectPayout($id, $request)
    {
        $adminId = auth()->user()->id;
        $payout = $this->payout_rep->find($id);

        if ($payout->approval_status !== 'pending') {
            throw new \Exception('Lệnh rút đã được xử lý.');
        }

        $reason = $request->input('reason');
        // Cập nhật trạng thái từ chối
        $payout->update([
            'approval_status' => 'rejected',
            'reject_reason' => $reason,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'frozen_amount' => $payout->net_amount,
            'payout_status' => 'failed',
        ]);
        $payout->load('shop.user');
        $this->notification_service->createNotification([
            'title' => 'Rút tiền bị từ chối',
            'message' => "Yêu cầu rút tiền #{$payout->payout_code} của bạn đã bị từ chối. Vui lòng kiểm tra lại thông tin hoặc liên hệ CSKH để được hỗ trợ.",
            'send_type' => 'to_shop',
            'receiver_ids' => [$payout->shop->owner_id]
        ]);

        Mail::to($payout->shop->user->email)->send(new PayoutRejectedMail($payout));
        return $payout;
    }

    public function retryPayout($id)
    {
        $payout = $this->payout_rep->find($id);

        if ($payout->approval_status !== 'rejected') {
            throw new \Exception("Yêu cầu này không bị từ chối.", 404);
        }
        $payout->load('shop');
        $payout->update([
            'approval_status' => 'pending',
            'reject_reason' => null,
            'payout_status' => 'pending',
            'processed_by' => null,
            'processed_at' => null,
            'created_at' => now(),
        ]);
        $this->notification_service->createNotification([
            'title' => 'Tạo lại yêu cầu rút tiền',
            'message' => "Shop {$payout->shop->shop_name} đã tạo lại yêu cầu rút tiền #{$payout->payout_code}. Vui lòng kiểm tra và xử lý.",
            'send_type' => 'to_admin',
            'receiver_ids' => null,
            'link' => env('FRONTEND_URL') . '/admin/lenh-rut-tien'
        ]);

        return $payout;
    }

}