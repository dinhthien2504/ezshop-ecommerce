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
        Schema::create('tbl_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_detail_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('rate');
            $table->json('images')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('rate_status')->default(1);

            $table->timestamps();

            // Foreign keys
            $table->foreign('order_detail_id')->references('id')->on('tbl_order_details')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_rates');
    }
};
