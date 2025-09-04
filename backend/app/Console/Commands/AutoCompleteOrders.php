<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OrderService;

class AutoCompleteOrders extends Command
{
    protected $signature = 'orders:auto-complete';
    protected $description = 'Tự động hoàn tất đơn hàng sau 7 ngày kể từ khi giao';

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        parent::__construct();
        $this->orderService = $orderService;
    }

    public function handle()
    {
        $count = $this->orderService->autoCompleteDeliveredOrders();
        $this->info(string: "Đã xử lý {$count} đơn hàng.");
    }
}