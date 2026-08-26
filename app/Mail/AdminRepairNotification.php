<?php

namespace App\Mail;

use App\Models\RepairSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminRepairNotification extends Mailable
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
            subject: 'Nieuwe reparatieaanvraag van '.$this->submission->name.' – Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-repair-notification',
            with: [
                'inboxUrl' => route('admin.reparatie-aanmeldingen.show', $this->submission->id),
            ],
        );
    }

    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+reply-repair-'.$this->submission->id.'@', $from, 1) ?? $from;
    }
}
