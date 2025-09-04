<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'tbl_notifications';

    protected $fillable = [
        'title',
        'message',
        'link',
        'sender_id',
        'send_type',
        'receiver_ids'
    ];

    protected $attributes = [
        'title' => '',
        'message' => '',
        'link' => null,
        'sender_id' => null,
        'send_type' => 'to_user',
        'receiver_ids' => null,
    ];

    protected $casts = [
        'receiver_ids' => 'array',
    ];

    public $timestamps = true;

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }

}
