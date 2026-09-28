<?php

namespace App\Mail;

use App\Models\LaptopLoan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class LaptopLoanReturnMail extends Mailable
{
    use Queueable;

    public function __construct(
        public LaptopLoan $loan
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bevestiging retour laptop ' . $this->loan->loanNumber() . ' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.laptop-loan-return',
            with: ['loan' => $this->loan],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
