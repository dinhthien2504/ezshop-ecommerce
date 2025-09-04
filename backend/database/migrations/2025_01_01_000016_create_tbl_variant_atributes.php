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
        Schema::create('tbl_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_value_id')->constrained('tbl_attribute_values');
            $table->foreignId('variant_id')->constrained('tbl_product_variants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_variant_atributes');
    }
};
