<?php

namespace App\Mail;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ChatTicketMail extends Mailable
{
    use Queueable;

    public function __construct(
        public ChatConversation $conversation,
        public string $ticketText
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket aangemaakt – Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-ticket-received',
        );
    }

    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+chat-'.$this->conversation->id.'@', $from, 1) ?? $from;
    }
}
