<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Refund;
use Carbon\Carbon;
use App\Services\RefundService; 

class AutoRejectRefunds extends Command
{
    protected $signature = 'refunds:auto-reject';
    protected $description = 'Tự động xử lý refund bị shop từ chối quá 3 ngày mà user không khiếu nại';

    public function handle()
    {
        $expiredRefunds = Refund::where('status', 'rejected_by_shop')
            ->where('updated_at', '<', Carbon::now()->subDays(3))
            ->get();

        if ($expiredRefunds->isEmpty()) {
            $this->info("Không có refund nào cần xử lý.");
            return Command::SUCCESS;
        }

        foreach ($expiredRefunds as $refund) {
            // cập nhật trạng thái cuối
            $refund->status = 'rejected_final';
            $refund->save();

            // gọi hàm rejectFinalRefund trong service
            app(RefundService::class)->rejectFinalRefund($refund);

            $this->info("Refund #{$refund->id} đã được tự động chuyển sang rejected_final");
        }

        return Command::SUCCESS;
    }
}
