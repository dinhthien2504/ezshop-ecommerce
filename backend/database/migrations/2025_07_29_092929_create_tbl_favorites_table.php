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
        Schema::create('tbl_favorites', function (Blueprint $table) {
    $table->id();

    // 🟢 Thêm cột trước
    $table->unsignedBigInteger('user_id');
    $table->unsignedBigInteger('product_id');

    // 🟢 Sau đó mới tạo foreign key
    $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('tbl_products')->onDelete('cascade');

    $table->unique(['user_id', 'product_id']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_favorites');
    }
};
