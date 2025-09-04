<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('tbl_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('tbl_users')->nullOnDelete();

            $table->enum('type', ['user', 'platform']);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('debit', 15, 2)->default(0)->comment('Số tiền nợ sàn, trừ dần từ ví hoặc đơn hàng sau');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tbl_wallets');
    }
};
