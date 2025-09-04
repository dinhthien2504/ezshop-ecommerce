<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Payout;

class PayoutRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payout;

    public function __construct(Payout $payout)
    {
        $this->payout = $payout;
    }

    public function build()
    {
        return $this->subject('Yêu cầu rút tiền bị từ chối')
            ->view('emails.rejected')
            ->with([
                'shopName' => $this->payout->shop->shop_name,
                'payoutCode' => $this->payout->payout_code,
                'rejectedAt' => $this->payout->processed_at->format('d/m/Y H:i'),
                'rejectedReason' => $this->payout->reject_reason,
            ]);
    }
}
