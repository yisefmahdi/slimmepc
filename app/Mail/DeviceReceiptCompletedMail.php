<?php

namespace App\Mail;

use App\Models\DeviceReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeviceReceiptCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public DeviceReceipt $receipt)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uw apparaat is gerepareerd! — SlimmePC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.device-receipt-completed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
