<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectFor($this->order).' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
            with: ['order' => $this->order],
        );
    }

    public static function texts(Order $order): array
    {
        $isPickup = $order->shipping_method === 'pickup';

        return match ($order->order_status) {
            'processing' => [
                'badge' => 'In behandeling',
                'subject' => 'We zijn met je bestelling aan de slag',
                'title' => 'We zijn met je bestelling aan de slag!',
                'intro' => 'Goed nieuws: we hebben je bestelling in behandeling genomen en maken alles voor je klaar.',
            ],
            'shipped' => [
                'badge' => $isPickup ? 'Klaar voor afhalen' : 'Verzonden',
                'subject' => $isPickup ? 'Je bestelling staat klaar voor afhalen' : 'Je bestelling is verzonden',
                'title' => $isPickup ? 'Je bestelling staat klaar!' : 'Je bestelling is verzonden!',
                'intro' => $isPickup
                    ? 'Je bestelling ligt klaar in onze winkel in Apeldoorn. Neem je bestelnummer mee bij het afhalen.'
                    : 'Je bestelling is onderweg! Je ontvangt hem binnenkort op het opgegeven adres.',
            ],
            'completed' => [
                'badge' => 'Afgerond',
                'subject' => 'Bedankt voor je bestelling',
                'title' => 'Bedankt voor je bestelling!',
                'intro' => 'Je bestelling is afgerond. Bedankt voor je vertrouwen in Slimme-PC — graag tot ziens!',
            ],
            'cancelled' => [
                'badge' => 'Geannuleerd',
                'subject' => 'Je bestelling is geannuleerd',
                'title' => 'Je bestelling is geannuleerd',
                'intro' => 'Je bestelling is geannuleerd. Neem contact met ons op als je hier vragen over hebt of als dit niet klopt.',
            ],
            default => [
                'badge' => 'In afwachting',
                'subject' => 'We hebben je bestelling ontvangen',
                'title' => 'We hebben je bestelling ontvangen!',
                'intro' => 'Bedankt voor je bestelling! Zodra de betaling binnen is gaan we ermee aan de slag.',
            ],
        };
    }

    protected function subjectFor(Order $order): string
    {
        return self::texts($order)['subject'];
    }
}
