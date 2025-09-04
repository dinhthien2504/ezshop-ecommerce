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
        Schema::create('tbl_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('sender_type'); // 'user' or 'ai'
            $table->unsignedBigInteger('sender_id');
            $table->string('receiver_type'); // 'user', 'ai'
            $table->unsignedBigInteger('receiver_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_conversations');
    }
};
