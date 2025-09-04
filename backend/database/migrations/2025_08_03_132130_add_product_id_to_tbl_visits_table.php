<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_visits', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('shop_id');
            $table->foreign('product_id')->references('id')->on('tbl_products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_visits', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
