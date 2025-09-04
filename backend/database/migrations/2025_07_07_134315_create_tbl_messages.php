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
        Schema::create('tbl_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('tbl_conversations')->onDelete('cascade');
            $table->string('sender_type');
            $table->unsignedBigInteger('sender_id');
            $table->string('type')->default('text'); 
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_messages');
    }
};
