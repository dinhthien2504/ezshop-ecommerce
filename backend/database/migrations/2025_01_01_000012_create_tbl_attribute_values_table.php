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
        Schema::create('tbl_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->string('value', 100);
            $table->timestamps();
            $table->foreignId('attribute_id')->constrained('tbl_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_attribute_values');
    }
};
