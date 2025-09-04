<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_refunds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('address_id');
            $table->enum('status', ['pending', 'approved', 'rejected_by_shop', 'escalated', 'rejected_final', 'processed'])->default('pending');
            $table->decimal('total_refund', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('tbl_orders')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->foreign('address_id')->references('id')->on('tbl_address')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_refunds');
    }
};
