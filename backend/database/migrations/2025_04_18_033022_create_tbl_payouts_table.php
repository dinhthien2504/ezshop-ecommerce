<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPayoutsTable extends Migration
{
    public function up()
    {
        Schema::create('tbl_payouts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('shop_id');
            $table->string('payout_code')->unique(); // Mã giao dịch rút tiền

            $table->decimal('net_amount', 15, 2)->default(0); // Số tiền thực nhận (gross - phí - thuế)

            $table->string('transaction_code')->nullable(); // Mã giao dịch ngân hàng (nếu có)
            $table->enum('payout_status', ['pending', 'processing', 'completed', 'failed'])->default('pending');

            $table->timestamp('requested_at')->nullable(); // Thời gian yêu cầu rút tiền
            $table->timestamp('processed_at')->nullable(); // Thời gian xử lý xong

            $table->unsignedBigInteger('processed_by')->nullable(); // Admin xử lý

            $table->timestamps();

            // Foreign keys
            $table->foreign('shop_id')->references('id')->on('tbl_shops')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('tbl_users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_payouts');
    }
}
