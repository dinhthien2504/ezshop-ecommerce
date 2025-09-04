<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToPayoutsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tbl_payouts', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('payout_status');
            $table->text('reject_reason')->nullable()->after('approval_status');
            $table->decimal('frozen_amount', 15, 2)->nullable()->after('reject_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_payouts', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'reject_reason', 'frozen_amount']);
        });
    }
}
