<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminContactNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ContactSubmission $submission
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuwe contactaanvraag van '.$this->submission->name.' – Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    /**
     * Same "+reply-{id}" alias as the confirmation e-mail, so an admin reply
     * to this notification also arrives back on the thread via IMAP.
     */
    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+reply-'.$this->submission->id.'@', $from, 1) ?? $from;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin-contact-notification',
            with: [
                'inboxUrl' => route('admin.contact-inbox.index', ['submission' => $this->submission->id]),
            ],
        );
    }
}