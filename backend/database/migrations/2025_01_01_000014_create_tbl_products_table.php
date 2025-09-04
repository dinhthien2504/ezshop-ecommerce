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
        Schema::create('tbl_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('tbl_categories');
            $table->foreignId('shop_id')->constrained('tbl_shops');
            $table->string('name');
            $table->string('slug', 255)->unique(); 
            $table->text('description');
            $table->decimal('weight', 8, 2)->default(0.00);
            $table->decimal('length', 8, 2)->default(0.00);
            $table->decimal('width', 8, 2)->default(0.00);
            $table->decimal('height', 8, 2)->default(0.00);

            //tình trạng, duyệt do ai + lúc nào, từ chối
            $table->tinyInteger('status')->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('tbl_users');
            $table->timestamp('approved_at')->nullable();

            $table->text('rejected_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_products');
    }
};
