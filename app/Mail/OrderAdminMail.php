<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

use Illuminate\Contracts\Queue\ShouldQueue;

class OrderAdminMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        return $this
            ->subject("Новый заказ #{$this->order->id}")
            ->view('emails.orders.admin');
    }
}
