<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'tbl_messages';
    // protected $casts = [
    //     'content' => 'array', // Laravel sẽ tự json_decode content khi lấy từ DB
    // ];
    protected $fillable = ['conversation_id', 'sender_type', 'sender_id', 'type', 'content', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function attachments()
    {
        return $this->hasMany(MessageAttachment::class, 'message_id');
    }

    public function sender()
    {
        return $this->morphTo(null, 'sender_type', 'sender_id');
    }

    public function receiver()
    {
        return $this->morphTo();
    }

}
