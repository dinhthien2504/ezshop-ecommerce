<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->decimal('platform_fee_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->dropColumn('platform_fee_amount');
            $table->dropColumn('tax_amount');
        });
    }
};