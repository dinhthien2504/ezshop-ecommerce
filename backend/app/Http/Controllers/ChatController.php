<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;

class ChatController extends Controller
{
    public function send(Request $request){
        $request->validate([
            'receiver_type' => 'required|string',
            'receiver_id' => 'required|integer',
            'message' => 'nullable',
            'attachments.*' => 'file|max:20480',
        ]);

        $user = Auth::user();
        $message = $request->input('message');
        $receiver_id = $request->input('receiver_id');
        $receiver_type = $request->input('receiver_type');
        
        // Kiểm tra cuộc trò chuyện đã tồn tại chưa
        $conversation = Conversation::findConversation('user', $user->id, $receiver_type, $receiver_id);
        
        // Nếu cuộc trò chuyện không tồn tại, tạo mới
        if (!$conversation) {
            $conversation = Conversation::create([
                'sender_type' => 'user',
                'sender_id' => $user->id,
                'receiver_type' => $receiver_type,
                'receiver_id' => $receiver_id,
            ]);
        }

        if (empty($request->message)) {
            return response()->json([
                'conversation_id' => $conversation->id,
                'conversation' => $conversation
            ]);
        }

        // Tạo tin nhắn mới trong cuộc trò chuyện
        // dd($request->message);
        $content = $request->message;
        if (!is_string($content)) {
            $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        }
        
        // \Log::info('DEBUG type', ['type' => $request->input('type'), 'real_type' => gettype($request->input('type'))]);
        // \Log::info('DEBUG content', ['content' => $content, 'type' => gettype($content)]);
        $message = $conversation->messages()->create([
            'sender_type' => 'user',
            'sender_id' => $user->id,
            'content' => $content,
            'type' => is_array($request->input('type')) ? json_encode($request->input('type')) : (string)$request->input('type', 'text'),
        ]);

        $attachments = $request->file('attachments', []);
        foreach ($attachments as $file) {
            $path = $file->store('chat/attachments', 'public');
            $message->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        broadcast(new MessageSent($message, $user))->toOthers();
        // broadcast(new MessageSent($message, $user));
        // \Log::info('✅ broadcast sent to others');

        return response()->json([
            'message' => $message->load(['attachments']),
            'conversation_id' => $conversation->id,
        ]);
    }

    public function get_conversation(){
        $user = Auth::user();

        $conversations = Conversation::with(['sender.shop', 'receiver.shop', 'lastMessage'])
            ->where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('sender_id', $user->id)
                        ->where('sender_type', 'user');
                })
                ->orWhere(function ($q2) use ($user) {
                    $q2->where('receiver_id', $user->id)
                        ->where('receiver_type', 'user');
                });
            })
            ->get()
            ->map(function ($c) use ($user) {
                $isSender = $c->sender_id == $user->id;
                $otherUser = $isSender ? $c->receiver : $c->sender;

                return [
                    'id' => $c->id,
                    'user_name' => $otherUser->user_name ?? null,
                    'receiver_id' => $otherUser->id ?? null,
                    'receiver_type' => $otherUser->getMorphClass() ?? null,
                    'shop_name' => $otherUser->shop->shop_name ?? null,
                    'shop_avatar' => $otherUser->shop->shop_image ?? null,
                    'last_message' => $c->lastMessage->content ?? null,
                    'last_message_type' => $c->lastMessage->type ?? null,
                    'is_read' => $c->lastMessage ? ($c->lastMessage->sender_id == $user->id ? true : (bool)$c->lastMessage->is_read): true,
                    'last_message_time' => optional($c->lastMessage)->created_at?->diffForHumans(),
                ];
            })
            ->values();

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    public function get_messages($conversation_id) {
        $user = Auth::user();

        $conversation = Conversation::with([
            'messages.attachments',
            'messages.sender.shop',
            'sender.shop',
            'receiver.shop'
        ])->findOrFail($conversation_id);

        if (!($conversation->sender_id == $user->id || $conversation->receiver_id == $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Xác định người nhận
        $receiver = $conversation->sender_id == $user->id
            ? $conversation->receiver
            : $conversation->sender;

        $messages = $conversation->messages->map(function ($message) use ($user) {
            $sender = $message->sender;

            return [
                'id' => $message->id,
                'content' => $message->content,
                'sender' => [
                    'id' => $message->sender_id,
                    'type' => $message->sender_type,
                    'user_name' => $sender->user_name ?? null,
                    'shop_name' => $sender->shop->shop_name ?? null,
                ],
                'attachments' => $message->attachments->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'file_name' => $attachment->file_name,
                        'file_path' => asset('storage/' . $attachment->file_path),
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                    ];
                }),
                'type' => $message->type,
                'created_at' => $message->created_at->toDateTimeString(),
                'is_self' => $message->sender_id == $user->id && $message->sender_type === 'user',
            ];
        });

        return response()->json([
            'messages' => $messages,
            'conversation_id' => $conversation_id,
            'receiver' => [
                'id' => $receiver->id,
                'user_name' => $receiver->user_name ?? null,
                'shop_name' => $receiver->shop->shop_name ?? null,
            ],
        ]);
    }

    public function mark_as_read($conversation_id) {
        $user = Auth::user();

        $conversation = Conversation::findOrFail($conversation_id);

        if (!($conversation->sender_id == $user->id || $conversation->receiver_id == $user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Cập nhật trạng thái đã đọc cho tất cả tin nhắn trong cuộc trò chuyện
        $count = Message::where('conversation_id', $conversation_id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'updated_rows' => $count]);
    }

    // public function mark_as_read($conversation_id) {
    //     $user = Auth::user();

    //     $conversation = Conversation::findOrFail($conversation_id);

    //     if (!($conversation->sender_id == $user->id || $conversation->receiver_id == $user->id)) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     // Xác định id của đối phương trong cuộc trò chuyện
    //     $otherId = $conversation->sender_id == $user->id
    //         ? $conversation->receiver_id
    //         : $conversation->sender_id;

    //     dd([
    //         'auth_id' => Auth::id(),
    //         'conversation_id' => $conversation_id,
    //         'count' => Message::where('conversation_id', $conversation_id)->count(),
    //         'unread_count' => Message::where('conversation_id', $conversation_id)
    //             ->where('sender_id', '!=', Auth::id())
    //             ->where('is_read', false)
    //             ->count(),
    //     ]);

    //     // Cập nhật trạng thái đã đọc
    //     $count = Message::where('conversation_id', $conversation_id)
    //         ->where('sender_id', $otherId) // chỉ tin của người kia
    //         ->where('is_read', false)
    //         ->update(['is_read' => true]);

    //     return response()->json([
    //         'status' => 'success',
    //         'updated_rows' => $count
    //     ]);
    // }


    // public function un_read_count() {
    //     $user = Auth::user();

    //     $unreadCount = Message::whereHas('conversation', function ($q) use ($user) {
    //         $q->where(function ($q2) use ($user) {
    //             $q2->where('sender_id', $user->id)
    //                 ->where('sender_type', 'user');
    //         })
    //         ->orWhere(function ($q2) use ($user) {
    //             $q2->where('receiver_id', $user->id)
    //                 ->where('receiver_type', 'user');
    //         });
    //     })
    //     ->where('is_read', false)
    //     ->where(function ($q) use ($user) {
    //         $q->where('sender_id', '!=', $user->id)
    //           ->orWhere('sender_type', '!=', 'user');
    //     })
    //     ->count();

    //     return response()->json(['unread_count' => $unreadCount]);
    // }

    // public function un_read_by_conversation($conversation_id) {
    //     $user = Auth::user();

    //     $conversation = Conversation::findOrFail($conversation_id);

    //     if (!($conversation->sender_id == $user->id || $conversation->receiver_id == $user->id)) {
    //         return response()->json(['error' => 'Unauthorized'], 403);
    //     }

    //     $unreadCount = Message::where('conversation_id', $conversation_id)
    //         ->where('is_read', false)
    //         ->where(function ($q) use ($user) {
    //             $q->where('sender_id', '!=', $user->id)
    //               ->orWhere('sender_type', '!=', 'user');
    //         })
    //         ->count();

    //     return response()->json(['unread_count' => $unreadCount]);
    // }

    // public function unreadTotal(){
    //     $count = Message::where('receiver_id', auth()->id())
    //         ->where('is_read', false)
    //         ->count();

    //     return response()->json(['unread_count' => $count]);
    // }

}