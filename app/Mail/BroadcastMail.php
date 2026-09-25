<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $username,
        public string $type,
        public string $content,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubjectLine(),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.broadcast',
            with: [
                'name' => $this->username,
                'content' => $this->content,
            ],
        );
    }

    private function getSubjectLine(): string
    {
        return match ($this->type) {
            'special_offer' => '🎁 Speciale aanbieding van SlimmePC!',
            'marketing' => '📈 Nieuw marketingbericht',
            'maintenance' => '🛠️ Onderhoudsmededeling',
            'important_notice' => '🚨 Belangrijke melding',
            default => 'Bericht van SlimmePC',
        };
    }
}
