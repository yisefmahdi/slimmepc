<?php

namespace App\Mail;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ChatClosedMail extends Mailable
{
    use Queueable;

    public function __construct(
        public ChatConversation $conversation
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bedankt voor je chat met Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-closed',
        );
    }

    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+chat-'.$this->conversation->id.'@', $from, 1) ?? $from;
    }
}
