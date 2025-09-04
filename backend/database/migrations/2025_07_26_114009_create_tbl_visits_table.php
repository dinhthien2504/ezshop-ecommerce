<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id'); // Shop được truy cập
            $table->string('type')->nullable(); // optional: 'shop', 'product', ...
            $table->ipAddress('ip_address')->nullable(); // IP người truy cập
            $table->string('user_agent')->nullable(); // Thông tin trình duyệt
            $table->timestamp('visited_at')->useCurrent(); // Thời điểm truy cập

            $table->foreign('shop_id')->references('id')->on('tbl_shops')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tbl_visits');
    }
};
