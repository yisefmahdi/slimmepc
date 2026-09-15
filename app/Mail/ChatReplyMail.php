<?php

namespace App\Mail;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ChatReplyMail extends Mailable
{
    use Queueable;

    public function __construct(
        public ChatConversation $conversation,
        public \App\Models\ChatMessage $reply
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: je chat met Slimme-PC',
            replyTo: [$this->replyToAddress()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.chat-reply',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $path = $this->reply->attachment;

        if (! $path || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
            return [];
        }

        return [
            Attachment::fromStorageDisk('local', $path)
                ->as(\Illuminate\Support\Str::afterLast($path, '/')),
        ];
    }

    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+chat-'.$this->conversation->id.'@', $from, 1) ?? $from;
    }
}
