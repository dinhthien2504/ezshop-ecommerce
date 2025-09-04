<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tbl_ranks')->insert([
            [
                'name' => 'Thành viên Mới',
                'min_spent' => 0,
                'max_spent' => 999000,
                'benefits' => 'Chào mừng thành viên mới! Nhận thông báo về các chương trình khuyến mãi đặc biệt.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thành viên Bạc',
                'min_spent' => 1000000,
                'max_spent' => 4999000,
                'benefits' => 'Giảm giá 2% cho tất cả đơn hàng + Miễn phí vận chuyển cho đơn hàng từ 500.000đ + Ưu tiên hỗ trợ khách hàng',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thành viên Vàng',
                'min_spent' => 5000000,
                'max_spent' => 14999000,
                'benefits' => 'Giảm giá 5% cho tất cả đơn hàng + Miễn phí vận chuyển toàn quốc + Voucher sinh nhật 200.000đ + Quyền truy cập sớm vào sale đặc biệt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thành viên Bạch Kim',
                'min_spent' => 15000000,
                'max_spent' => 29999000,
                'benefits' => 'Giảm giá 8% cho tất cả đơn hàng + Miễn phí vận chuyển nhanh + Voucher sinh nhật 500.000đ + Tư vấn cá nhân hóa + Đổi trả trong 30 ngày',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Thành viên Kim Cương',
                'min_spent' => 30000000,
                'max_spent' => 999999999,
                'benefits' => 'Giảm giá 12% cho tất cả đơn hàng + Miễn phí vận chuyển cao cấp + Voucher sinh nhật 1.000.000đ + Chăm sóc khách hàng VIP 24/7 + Trải nghiệm sản phẩm trước khi mua + Quà tặng đặc biệt theo quý',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
