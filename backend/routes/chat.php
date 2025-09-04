<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// routes/web.php hoặc routes/api.php
Route::get('/test-broadcast', function () {
    $message = App\Models\Message::latest()->first();
    $user = auth()->user();
    Broadcast(new MessageSent($message, $user));

    return response()->json(['success' => true]);
})->middleware('auth:sanctum');


// Realtime chat
Route::post('/chat', [ChatController::class, 'send'])->middleware('auth:sanctum');

Route::get('/conversation', [ChatController::class, 'get_conversation'])->middleware('auth:sanctum');

Route::get('/conversation/{conversation_id}/messages', [ChatController::class, 'get_messages'])->middleware('auth:sanctum');

Route::post('/conversation/{conversation_id}/mark-as-read', [ChatController::class, 'mark_as_read'])->middleware('auth:sanctum');