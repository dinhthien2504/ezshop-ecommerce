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
        Schema::create('tbl_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('tbl_products');
            $table->string('url', 255);
            $table->tinyInteger('is_main')->default(0);
            $table->enum('type', ['image', 'video']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_medias'); // ❌ Bạn để nhầm là 'tbl_images' trước đó
    }
};

