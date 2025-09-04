<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethod extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_payment_methods')->insert([
            //Thanh toán khi nhận hàng
            [
                'title' => 'Thanh toán khi nhận hàng',
                'description' => 'Trả tiền mặt khi nhận hàng.',
                'url' => 'pay-code.png',
                'is_active' => true,
            ],
            //Thanh toán ví vnpay
            [
                'title' => 'Ví VNPay',
                'description' => 'Thanh toán qua cổng VNPay bằng thẻ ngân hàng nội địa hoặc thẻ quốc tế.',
                'url' => 'pay-vnpay.png',
                'is_active' => true,
            ],
            //Thanh toán MoMo
            [
                'title' => 'Ví MoMo',
                'description' => 'Thanh toán qua cổng thanh toán MoMo.',
                'url' => 'pay-momo.png',
                'is_active' => true,
            ]
        ]);
    }
}
