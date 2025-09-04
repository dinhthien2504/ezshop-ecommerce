<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRead extends Model
{
    protected $table = 'tbl_notification_reads';

    protected $fillable = [
        'notification_id',
        'user_id',
        'read_at',
    ];

    public $timestamps = false;

    public function notification()
    {
        return $this->belongsTo(Notification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
