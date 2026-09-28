<?php

namespace App\Mail;

use App\Models\LaptopLoan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class LaptopLoanMail extends Mailable
{
    use Queueable;

    public function __construct(
        public LaptopLoan $loan
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bevestiging lenen laptop ' . $this->loan->loanNumber() . ' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.laptop-loan',
            with: ['loan' => $this->loan],
        );
    }

    public function attachments(): array
    {
        if ($this->loan->pdf_path && Storage::disk('local')->exists($this->loan->pdf_path)) {
            return [
                Attachment::fromPath(Storage::disk('local')->path($this->loan->pdf_path))
                    ->as($this->loan->loanNumber() . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
