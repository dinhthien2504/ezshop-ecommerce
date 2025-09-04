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
        Schema::create('tbl_violations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('reporter_id');
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('violation_type_id');

            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'ignored', 'resolved'])->default('pending');

            $table->timestamps();

            $table->foreign('reporter_id')->references('id')->on('tbl_users')->onDelete('cascade');
            $table->foreign('violation_type_id')->references('id')->on('tbl_violation_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_violations');
    }
};
