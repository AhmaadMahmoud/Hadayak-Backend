<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎁 طلبك وصلنا! — '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmed',
            with: [
                'order' => $this->order->load(['items', 'user', 'address', 'wrapOption', 'cardDesign']),
            ],
        );
    }
}
