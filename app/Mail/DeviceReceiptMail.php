<?php

namespace App\Mail;

use App\Models\DeviceReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class DeviceReceiptMail extends Mailable
{
    use Queueable;

    public function __construct(
        public DeviceReceipt $receipt
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bevestiging Ontvangst Apparaat - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.device-receipt',
            with: ['receipt' => $this->receipt],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
