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
        Schema::create('tbl_banners', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('image');                              // Đường dẫn ảnh
            $table->string('link')->nullable();                   // URL khi click vào banner
            $table->enum('position', [
                'home_1',
                'home_2',
                'home_3',
                'mobile_1',
                'mobile_2',
                'mobile_3',
                'slider',
            ])->nullable();                                      // Vị trí hiển thị 

            $table->integer('priority')->default(0);              // Mức độ ưu tiên
            $table->timestamp('start_at')->nullable();            // Thời gian bắt đầu hiển thị
            $table->timestamp('end_at')->nullable();                  // Thời gian kết thúc hiển thị

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_banners');
    }
};
