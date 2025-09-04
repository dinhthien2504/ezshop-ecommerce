<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_refund_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('refund_id');
            $table->unsignedBigInteger('order_detail_id');
            $table->unsignedBigInteger('variant_id');
            $table->integer('qty');

            // Giá gốc của 1 sản phẩm tại thời điểm mua
            $table->decimal('price_original', 10, 2);
            // Tổng tiền (qty * price_original)
            $table->decimal('subtotal', 10, 2);

            // Phần chiết khấu (khuyến mãi/voucher phân bổ vào item này)
            $table->decimal('discount_allocated', 10, 2)->default(0);

            // Số tiền yêu cầu hoàn lại cho item này (sau khi trừ chiết khấu, tính theo qty)
            $table->decimal('refund_amount', 10, 2);

            $table->timestamps();

            // Lý do chính của việc hoàn trả
            $table->enum('reason_type', ['seller_fault', 'buyer_fault', 'shipping_fault', 'other'])->default('other');

            // Mô tả chi tiết lý do (vd: hàng vỡ, giao nhầm size…)
            $table->text('reason_detail')->nullable();

            // Ảnh minh chứng (lưu dạng JSON array các đường dẫn ảnh)
            $table->json('evidences')->nullable();
            // phí ship trả hàng của item
            $table->decimal('shipping_fee', 10, 2)->default(0);
            // ai chịu phí ship
            $table->enum('shipping_payer', ['platform', 'seller', 'buyer'])->default('seller'); 

            // Khóa ngoại
            $table->foreign('refund_id')->references('id')->on('tbl_refunds')->cascadeOnDelete();
            $table->foreign('order_detail_id')->references('id')->on('tbl_order_details')->cascadeOnDelete();
            $table->foreign('variant_id')->references('id')->on('tbl_product_variants')->restrictOnDelete();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_refund_items');
    }
};