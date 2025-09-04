<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tbl_users');
            $table->foreignId('shop_id')->constrained('tbl_shops');
            $table->foreignId('payment_method_id')->constrained('tbl_payment_methods');
            $table->foreignId('address_id')->constrained('tbl_address');
            // ['Chờ xác nhận', 'Đã xác nhận', 'Chờ giao hàng', 'Hoàn thành', 'Đã hủy', 'Trả hàng/Hoàn tiền']
            $table->tinyInteger('order_status')->default(1);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->datetime('created_date');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_orders');
    }
};
