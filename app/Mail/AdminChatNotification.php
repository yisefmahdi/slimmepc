<?php

namespace App\Mail;

use App\Models\ChatConversation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class AdminChatNotification extends Mailable
{
    use Queueable;

    /**
     * @param 'offline'|'handover' $reason
     */
    public function __construct(
        public ChatConversation $conversation,
        public string $reason = 'offline',
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = $this->reason === 'handover'
            ? 'Chat: klant vraagt om een medewerker – Slimme-PC'
            : 'Chat: nieuw offline bericht – Slimme-PC';

        return new Envelope(
            subject: $subject,
            replyTo: [$this->replyToAddress()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-chat-notification',
        );
    }

    /**
     * Foto's van de klant mee als bijlage (max 3, max 10MB totaal).
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $files = [];
        $total = 0;

        $photos = $this->conversation->messages()
            ->where('sender', 'customer')
            ->whereNotNull('attachment')
            ->orderByDesc('id')
            ->limit(5)
            ->pluck('attachment');

        foreach ($photos as $path) {
            if (! $path || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($path)) {
                continue;
            }
            $size = \Illuminate\Support\Facades\Storage::disk('local')->size($path);
            if ($total + $size > 10 * 1024 * 1024 || count($files) >= 3) {
                break;
            }
            $total += $size;
            $files[] = Attachment::fromStorageDisk('local', $path)
                ->as(\Illuminate\Support\Str::afterLast($path, '/'));
        }

        return $files;
    }

    /**
     * Reply-To draagt de +chat-{id} alias: antwoordt de medewerker vanuit
     * zijn eigen mailprogramma, dan routeert het antwoord terug de thread in.
     */
    private function replyToAddress(): string
    {
        $from = config('mail.from.address', 'info@slimme-pc.nl');

        return preg_replace('/@/', '+chat-'.$this->conversation->id.'@', $from, 1) ?? $from;
    }
}
