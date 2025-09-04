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
        Schema::create('tbl_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('tbl_notifications')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tbl_users')->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent(); // Thời gian đọc
            // $table->timestamps();
            
            // Unique constraint: 1 user chỉ có 1 read record cho 1 notification
            $table->unique(['notification_id', 'user_id']);
            
            // Index để optimize query
            $table->index(['user_id']);
            $table->index(['notification_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_notification_reads');
    }
};
