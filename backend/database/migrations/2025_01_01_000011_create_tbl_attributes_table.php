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
        Schema::create('tbl_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
            // Đặt mặc định là null cho shop_id
            $table->unsignedBigInteger('shop_id')->nullable()->default(null);
            // Đảm bảo rằng khóa ngoại sẽ tham chiếu đến bảng tbl_shops
            $table->foreign('shop_id')->references('id')->on('tbl_shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_attributes');
    }
};
