<?php
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation; 

Broadcast::routes(['middleware' => ['broadcast.bearer']]);

Broadcast::channel('chat.{conversation_id}', function ($user, $conversation_id) {
    // \Log::info('🔑 Authenticated user in channel:', [$user]);
    return Conversation::where('id', $conversation_id)
        ->where(function($q) use($user) {
            $q->where('sender_type', 'user')->where('sender_id', $user->id)
              ->orWhere('receiver_type', 'user')->where('receiver_id', $user->id);
        })->exists();
});



