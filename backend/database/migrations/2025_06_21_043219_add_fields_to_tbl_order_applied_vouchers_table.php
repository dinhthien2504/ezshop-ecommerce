<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_order_applied_vouchers', function (Blueprint $table) {
            $table->enum('type', ['platform', 'shop', 'shipping']);
            $table->bigInteger('shop_id')->nullable()->constrained('tbl_shops');
            $table->decimal('discount_amount', 10,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_order_applied_vouchers', function (Blueprint $table) {
            //
        });
    }
};
