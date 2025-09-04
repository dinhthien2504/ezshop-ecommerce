<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('tbl_wallets')->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->enum('type', [
                'order_payment',   // user trả tiền, vào ví platform
                'order_release_platform',// platform chi tiền cho seller
                'order_release_seller',
                'refund',          // platform -> user
                'withdraw',        // seller rút
                'deposit',         // user nạp (nếu có)
                'adjustment',     // admin chỉnh tay
                'debit',           // Ghi nợ
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_wallet_transactions');
    }
};
