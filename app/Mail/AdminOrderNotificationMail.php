<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuwe bestelling '.$this->order->order_number.' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-order-notification',
            with: [
                'order' => $this->order,
                'adminUrl' => route('admin.orders.show', $this->order),
            ],
        );
    }
}
