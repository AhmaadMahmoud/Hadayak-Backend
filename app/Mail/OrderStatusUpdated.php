<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        $emoji = [
            'confirmed' => '✅',
            'preparing' => '🎀',
            'delivering' => '🚚',
            'delivered' => '🎉',
            'cancelled' => '😢',
        ][$this->order->status] ?? '🎁';

        return new Envelope(
            subject: $emoji.' طلبك '.(Order::STATUSES[$this->order->status] ?? $this->order->status).' — '.$this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status-updated',
            with: [
                'order' => $this->order->load(['items', 'user']),
            ],
        );
    }
}
