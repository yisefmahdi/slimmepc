<?php

namespace App\Mail;

use App\Models\ContactSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactReplyMail extends Mailable
{
    use Queueable;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ContactSubmission $submission,
        public string $replyBody,
        public string $adminName,
        public ?string $attachment = null,
    ) {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: '.$this->submission->subject.' – Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    /**
     * The reply-to address carries the same "+reply-{id}" alias as the
     * confirmation e-mail, so a customer replying to this message routes
     * back onto the dashboard thread via IMAP.
     */
    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+reply-'.$this->submission->id.'@', $from, 1) ?? $from;
    }

    /**
     * Attach the file the admin added from the dashboard (if any).
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (! $this->attachment) {
            return [];
        }

        $path = storage_path('app/private/contact/'.$this->submission->id.'/'.$this->attachment);

        if (! is_file($path)) {
            return [];
        }

        return [
            \Illuminate\Mail\Mailables\Attachment::fromPath($path)
                ->as(\Illuminate\Support\Str::afterLast($this->attachment, '/')),
        ];
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-reply',
        );
    }
}