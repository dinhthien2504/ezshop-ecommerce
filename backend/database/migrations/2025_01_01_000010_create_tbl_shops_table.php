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
        Schema::create('tbl_shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('tbl_users');
            $table->string('shop_name', 100)->unique();
            $table->string('pickup_name', 100);
            $table->string('phone', 20);
            $table->string('image', 100)->nullable();
            $table->string('background', 100)->nullable();
            $table->string('detail_address', 255);
            $table->bigInteger('province_id');
            $table->bigInteger('district_id');
            $table->string('ward_code', 20);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_shops');
    }
};
