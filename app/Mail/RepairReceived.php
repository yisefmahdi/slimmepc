<?php

namespace App\Mail;

use App\Models\RepairSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class RepairReceived extends Mailable
{
    use Queueable;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public RepairSubmission $submission
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We hebben je reparatieaanvraag ontvangen – Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.repair-received',
        );
    }

    /**
     * Unique "+reply-repair-{id}" alias so a customer reply lands in the
     * same inbox (matched back via IMAP if configured).
     */
    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+reply-repair-'.$this->submission->id.'@', $from, 1) ?? $from;
    }
}
