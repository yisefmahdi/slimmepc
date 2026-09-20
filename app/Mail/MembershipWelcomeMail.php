<?php

namespace App\Mail;

use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class MembershipWelcomeMail extends Mailable
{
    use Queueable;

    public function __construct(
        public Membership $membership
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welkom als lid bij Slimme-PC (' . $this->membership->klantnummer . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.membership-welcome',
            with: [
                'membership' => $this->membership,
                'invoice' => $this->membership->invoices()->latest('id')->first(),
            ],
        );
    }

    public function attachments(): array
    {
        $invoice = $this->membership->invoices()->latest('id')->first();

        if ($invoice && $invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            return [
                Attachment::fromPath(Storage::disk('local')->path($invoice->pdf_path))
                    ->as($invoice->invoice_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
