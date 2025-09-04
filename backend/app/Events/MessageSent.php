<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;


class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $user;
    // public $unread_count;
    // public $chat_controller;

    public function __construct(Message $message, User $user)
    {
        $this->message = $message;
        $this->user = $user;
        // $this->chat_controller = new \App\Http\Controllers\ChatController();
        // $this->unread_count = $this->chat_controller->get_unread_count($user->id);
        // \Log::info('🔥 MessageSent fired from Tinker', [
        // 'message_id' => $message->id,
        // 'user_id' => $user->id,
        // ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        // \Log::info('📡 Broadcasting to:', ['chat.' . $this->message->conversation_id]);
        // return new Channel('chat');
        return new PrivateChannel('chat.' . $this->message->conversation_id);
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }

    public function broadcastWith()
    {
        // \Log::info('📤 broadcastWith:', [
        //     'msg' => $this->message->content,
        //     'user_id' => $this->user->id,
        // ]);

        return [
            'message' => [
                'conversation_id' => $this->message->conversation_id,
                'content' => $this->message->content,
                'type' => $this->message->type,
                'created_at' => $this->message->created_at->toDateTimeString(),
                'is_self' => false,
                'is_read' => false,
            ]
        ];
    }

    public function broadcastExcept()
    {
        return [$this->user->id];
    }
    

}
