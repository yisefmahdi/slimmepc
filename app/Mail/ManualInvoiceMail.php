<?php

namespace App\Mail;

use App\Models\ManualInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class ManualInvoiceMail extends Mailable
{
    use Queueable;

    public function __construct(
        public ManualInvoice $invoice
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uw Factuur ' . $this->invoice->invoice_number . ' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.manual-invoice',
            with: ['invoice' => $this->invoice],
        );
    }

    public function attachments(): array
    {
        if ($this->invoice->pdf_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($this->invoice->pdf_path)) {
            return [
                Attachment::fromPath(Storage::disk('local')->path($this->invoice->pdf_path))
                    ->as($this->invoice->invoice_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
