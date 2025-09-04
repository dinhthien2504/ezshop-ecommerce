<?php

namespace App\Services;

use App\Repositories\WalletEloquentRepository;
use App\Repositories\WalletTransactionEloquentRepository;
use Illuminate\Support\Facades\DB;

class WalletService
{
    protected $wallet_repo;
    protected $wallet_transaction_repo;

    public function __construct(
        WalletEloquentRepository $wallet_repo,
        WalletTransactionEloquentRepository $wallet_transaction_repo,
    ) {
        $this->wallet_repo = $wallet_repo;
        $this->wallet_transaction_repo = $wallet_transaction_repo;
    }

    public function getWalletPlatform()
    {
        return $this->wallet_repo->getWalletPlatform();
    }

    public function getWalletByUser()
    {
        $user = auth()->user();
        if (!$user) {
            throw new \Exception("User not found", 404);
        }
        $wallet = $this->wallet_repo->getWalletByUser($user->id);
        return $wallet ? $wallet->balance : 0;
    }

    public function updateWalletBalance(
        int $walletId,
        float $amount,
        string $type,          // order_payment, order_release, refund...
        ?int $orderId = null,
        ?string $note = null
    ) {
        return DB::transaction(function () use ($walletId, $amount, $type, $orderId, $note) {
            $wallet = $this->wallet_repo->find($walletId);

            if (!$wallet) {
                throw new \Exception("Wallet not found");
            }

            // Map type -> credit/debit
            $directionMap = [
                'order_payment' => 'credit',   // user trả tiền => vào ví platform
                'order_release_platform' => 'debit',   // platform chi tiền cho seller
                'order_release_seller' => 'credit',    // seller nhận tiền từ platform
                'refund' => 'debit',    // platform trả user => ra
                'withdraw' => 'debit',    // seller rút
                'deposit' => 'credit',   // user nạp
                'adjustment' => 'credit',   // hoặc debit tùy trường hợp
            ];

            if (!isset($directionMap[$type])) {
                throw new \Exception("Unknown transaction type: {$type}");
            }

            $direction = $directionMap[$type];
            $balanceBefore = $wallet->balance;

            // Cộng/trừ theo hướng tiền
            if ($direction === 'credit') {
                $newBalance = $balanceBefore + $amount;
            } else {
                if ($balanceBefore < $amount) {
                    throw new \Exception("Insufficient balance");
                }
                $newBalance = $balanceBefore - $amount;
            }

            // Cập nhật số dư
            $this->wallet_repo->updateBalance($walletId, $newBalance);

            // Lưu lịch sử transaction
            $this->wallet_transaction_repo->createTransaction(
                $walletId,
                $amount,
                $type,
                $newBalance,
                $note,
                $orderId,
            );

            return $newBalance;
        });
    }


}