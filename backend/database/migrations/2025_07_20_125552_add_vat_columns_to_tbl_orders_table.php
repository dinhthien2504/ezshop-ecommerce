<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->decimal('total_vat_amount', 15, 2)->nullable()->after('total_amount');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_orders', function (Blueprint $table) {
            $table->dropColumn('total_vat_amount');
        });
    }
};