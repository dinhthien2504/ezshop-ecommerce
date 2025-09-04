<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\VariantAttributeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order->load([
            'user',
            'shop',
            'orderDetails.productVariant.product',
            'orderDetails.productVariant.variantAttribute',
            'address',
            'paymentMethod'
        ]);
        // Gắn thêm full_name_variant vào mỗi orderDetail
        foreach ($this->order->orderDetails as $detail) {
            $detail->full_name_variant = VariantAttributeService::getFullNameVariant(
                $detail->productVariant->variantAttribute
            );
        }
    }

    public function build()
    {
        return $this->subject('Xác nhận đơn hàng #' . $this->order->id)
            ->markdown('emails.orders.created');
    }
}
