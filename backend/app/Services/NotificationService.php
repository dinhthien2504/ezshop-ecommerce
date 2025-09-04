<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\ShopFollower;
use Illuminate\Support\Facades\Auth;
class NotificationService
{

    protected function getFollowerIds($shopId)
    {
        return ShopFollower::where('shop_id', $shopId)
            ->pluck('user_id')
            ->toArray();
    }

    public function createNotification($data): Notification
    {
        $data['sender_id'] = Auth::id() ?? 7;

        if (!empty($data['is_send_follower']) && $data['is_send_follower']) {
            $data['receiver_ids'] = $this->getFollowerIds($data['shop_id']);
        }

        if (empty($data['receiver_ids'])) {
            $data['receiver_ids'] = null; // Broadcast
        }

        $notification = Notification::create($data);

        return $notification;
    }
}