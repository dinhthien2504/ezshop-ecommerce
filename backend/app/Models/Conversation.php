<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $table = 'tbl_conversations';

    protected $fillable = ['sender_type', 'sender_id', 'receiver_type', 'receiver_id'];

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->morphTo(null, 'sender_type', 'sender_id');
    }

    public function receiver()
    {
        return $this->morphTo(null, 'receiver_type', 'receiver_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'conversation_id')->latest();
    }

    //Tìm kiếm cuộc trò chuyện giữa hai người dùng hoặc giữa người dùng và AI
    public static function findConversation($typeA, $idA, $typeB, $idB)
    {
        //Hàm ẩn danh use các biến ở ngoài vào trong closure
        return self::where(function ($query) use ($typeA, $idA, $typeB, $idB){
            $query->where('sender_type', $typeA) ->where('sender_id', $idA)
                ->where('receiver_type', $typeB) ->where('receiver_id', $idB);
        })->orWhere(function ($query) use ($typeA, $idA, $typeB, $idB) {
            $query->where('sender_type', $typeB) ->where('sender_id', $idB)
                ->where('receiver_type', $typeA) ->where('receiver_id', $idA);
        })->first();
    }

}
