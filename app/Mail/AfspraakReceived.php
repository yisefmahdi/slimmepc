<?php

namespace App\Mail;

use App\Models\AfspraakSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AfspraakReceived extends Mailable
{
    use Queueable, SerializesModels;

    public AfspraakSubmission $submission;

    /**
     * Create a new message instance.
     */
    public function __construct(AfspraakSubmission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $replyTo = config('contact-inbox.reply_to');

        return new Envelope(
            subject: 'Bevestiging van uw afspraak aan huis – ' . $this->submission->afspraak_number,
            replyTo: $replyTo ? [new Address($replyTo, 'Slimme-PC')] : [],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.afspraak-received',
            with: [
                'submission' => $this->submission,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
