<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->decimal('amount_to_seller', 15, 0)->after('total_amount')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->dropColumn('amount_to_seller');
        });
    }
};