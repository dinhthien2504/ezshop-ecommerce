<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable implements MustVerifyEmail
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'tbl_users';

    protected $fillable = [
        'user_name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'avatar',
        'role_id',
        'rank_id',
        'status',
        'google_id',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // app/Models/User.php
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'owner_id', 'id');
    }

    public function conversations()
    {
        return $this->morphMany(Conversation::class, 'sender')->orWhereMorph('receiver', 'user');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function messageAttachments()
    {
        return $this->hasMany(MessageAttachment::class, 'sender_id', 'id');
    }

    public function hasPermission($permission)
    {
        return $this->role
            ->rolePermissions()
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('title', $permission);
            })
            ->where('permission_value', 1)
            ->exists();
    }

    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}
