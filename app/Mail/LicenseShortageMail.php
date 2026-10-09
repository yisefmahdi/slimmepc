<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LicenseShortageMail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<int, array{title: string, quantity: int, available: int}> */
    public array $shortages;

    public function __construct(public Order $order, array $shortages = [])
    {
        $this->shortages = $shortages;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Licentiecode tekort — bestelling '.$this->order->order_number.' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.license-shortage',
            with: [
                'order' => $this->order,
                'shortages' => $this->shortages,
                'adminUrl' => route('admin.orders.show', $this->order),
            ],
        );
    }
}
