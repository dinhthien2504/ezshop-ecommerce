<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // \Log::info('✅ BroadcastServiceProvider booted');
        
        // Đăng ký route cần thiết để xác thực channel
        Broadcast::routes(['middleware' => ['auth:api']]);

        // Load file routes/channels.php để định nghĩa quyền truy cập channel
        require base_path('routes/channels.php');
    }
}
