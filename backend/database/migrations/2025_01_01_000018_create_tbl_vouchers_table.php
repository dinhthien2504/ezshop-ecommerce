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

        Schema::create('tbl_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->nullable()->constrained('tbl_shops');
            $table->string('code')->unique();
            $table->enum('type', ['fixed_amount', 'percentage_discount', 'free_shipping']);
            $table->enum('source', ['platform', 'shop']);
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('limit')->default(1);
            $table->decimal('min', 15, 0)->nullable();
            $table->decimal('max', 15, 0)->nullable();
            $table->unsignedTinyInteger('percent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_vouchers');
    }
};
