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
        Schema::create('tbl_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('tbl_products');
            $table->decimal('price', 15, 0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('sell')->default(0);
            $table->string('sku', 50)->unique();
            $table->string('image', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_product_variants');
    }
};
