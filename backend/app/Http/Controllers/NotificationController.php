<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\ShopFollower;

class NotificationController extends Controller
{

    protected $notification_service;

    public function __construct(
        NotificationService $notification_service
    ) {
        $this->notification_service = $notification_service;
    }
    public function create(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'link' => 'nullable|url',
                'send_type' => 'required|in:to_user,to_shop,to_admin',
                'receiver_ids' => 'nullable|array',
                'receiver_ids.*' => 'integer|exists:tbl_users,id',
                'is_send_follower' => 'nullable|boolean',
                'shop_id' => 'required_if:is_send_follower,true|exists:tbl_shops,id'
            ]);

            $notification = $this->notification_service->createNotification($data);
            return response()->json([
                'message' => 'Notification created successfully.',
                'notification' => $notification
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Failed to create notification.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();
        $sendType = $request->input('send_type');

        // Lấy tất cả notifications chưa đọc của user theo send_type
        $notifications = Notification::where('send_type', $sendType)
            ->where(function ($query) use ($user) {
                // Broadcast notifications (receiver_ids = null)
                $query->whereNull('receiver_ids')
                    // Hoặc targeted notifications (receiver_ids chứa user ID)
                    ->orWhereJsonContains('receiver_ids', $user->id);
            })
            ->whereDoesntHave('reads', function ($query) use ($user) {
                // Loại bỏ notifications đã có read record
                $query->where('user_id', $user->id);
            })
            ->get();

        // Tạo read records cho tất cả notifications chưa đọc
        $readRecords = [];
        foreach ($notifications as $notification) {
            $readRecords[] = [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'read_at' => now(),
            ];
        }

        // Bulk insert để tối ưu performance
        if (!empty($readRecords)) {
            NotificationRead::insert($readRecords);
        }

        return response()->json([
            'message' => 'All notifications marked as read.',
            'marked_count' => count($readRecords)
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();

        // Kiểm tra nếu đã có read record thì không làm gì
        $existing = NotificationRead::where([
            'notification_id' => $id,
            'user_id' => $user->id
        ])->first();

        if ($existing) {
            return response()->json([
                'message' => 'Notification already marked as read.'
            ], 200);
        }

        // Tạo read record mới
        NotificationRead::create([
            'notification_id' => $id,
            'user_id' => $user->id,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => 'Notification marked as read.'
        ], 200);
    }

    public function getUserNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where(function ($query) use ($user) {
            // Broadcast notifications (receiver_ids = null)
            $query->whereNull('receiver_ids')
                // Hoặc targeted notifications (receiver_ids chứa user ID)
                ->orWhereJsonContains('receiver_ids', $user->id);
        })
            ->where('send_type', 'to_user')
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) use ($user) {
                // Kiểm tra trạng thái đã đọc
                $isRead = NotificationRead::where([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id
                ])->exists();

                // Lấy thời gian đọc nếu có
                $readAt = null;
                if ($isRead) {
                    $readRecord = NotificationRead::where([
                        'notification_id' => $notification->id,
                        'user_id' => $user->id
                    ])->first();
                    $readAt = $readRecord ? $readRecord->read_at : null;
                }

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'send_type' => $notification->send_type,
                    'sender' => $notification->sender,
                    'created_at' => $notification->created_at,
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                ];
            });

        // Đếm số lượng chưa đọc
        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $notifications->count()
        ]);
    }

    public function getAdminNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where(function ($query) use ($user) {
            // Broadcast notifications (receiver_ids = null)
            $query->whereNull('receiver_ids')
                // Hoặc targeted notifications (receiver_ids chứa user ID)
                ->orWhereJsonContains('receiver_ids', $user->id);
        })
            ->where('send_type', 'to_admin')
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) use ($user) {
                // Kiểm tra trạng thái đã đọc
                $isRead = NotificationRead::where([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id
                ])->exists();

                // Lấy thời gian đọc nếu có
                $readAt = null;
                if ($isRead) {
                    $readRecord = NotificationRead::where([
                        'notification_id' => $notification->id,
                        'user_id' => $user->id
                    ])->first();
                    $readAt = $readRecord ? $readRecord->read_at : null;
                }

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'send_type' => $notification->send_type,
                    'sender' => $notification->sender,
                    'created_at' => $notification->created_at,
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                ];
            });

        // Đếm số lượng chưa đọc
        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $notifications->count()
        ]);
    }

    public function getShopNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where(function ($query) use ($user) {
            // Broadcast notifications (receiver_ids = null)
            $query->whereNull('receiver_ids')
                // Hoặc targeted notifications (receiver_ids chứa user ID)
                ->orWhereJsonContains('receiver_ids', $user->id);
        })
            ->where('send_type', 'to_shop')
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) use ($user) {
                // Kiểm tra trạng thái đã đọc
                $isRead = NotificationRead::where([
                    'notification_id' => $notification->id,
                    'user_id' => $user->id
                ])->exists();

                // Lấy thời gian đọc nếu có
                $readAt = null;
                if ($isRead) {
                    $readRecord = NotificationRead::where([
                        'notification_id' => $notification->id,
                        'user_id' => $user->id
                    ])->first();
                    $readAt = $readRecord ? $readRecord->read_at : null;
                }

                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'link' => $notification->link,
                    'send_type' => $notification->send_type,
                    'sender' => $notification->sender,
                    'created_at' => $notification->created_at,
                    'is_read' => $isRead,
                    'read_at' => $readAt,
                ];
            });

        // Đếm số lượng chưa đọc
        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $notifications->count()
        ]);
    }
}
