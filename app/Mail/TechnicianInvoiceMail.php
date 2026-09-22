<?php

namespace App\Mail;

use App\Models\TechnicianForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class TechnicianInvoiceMail extends Mailable
{
    use Queueable;

    public function __construct(
        public TechnicianForm $form
    ) {
    }

    public function envelope(): Envelope
    {
        $invoice = $this->form->technicianInvoice;

        return new Envelope(
            subject: 'Uw factuur ' . ($invoice?->invoice_number ?? '') . ' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.technician-invoice',
            with: [
                'form' => $this->form,
                'invoice' => $this->form->technicianInvoice,
            ],
        );
    }

    public function attachments(): array
    {
        $invoice = $this->form->technicianInvoice;

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
