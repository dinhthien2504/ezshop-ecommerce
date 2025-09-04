<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Product;

class ProductRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $product;
    public $reason;

    public function __construct(Product $product, $reason)
    {
        $this->product = $product;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Sản phẩm bị từ chối')
            ->view('emails.product_rejected')
            ->with([
                'shopName' => $this->product->shop->shop_name,
                'productName' => $this->product->name,
                'rejectedReason' => $this->reason,
            ]);
    }
}
