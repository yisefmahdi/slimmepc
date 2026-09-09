<?php

namespace App\Mail;

use App\Models\OrderInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public OrderInvoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uw factuur '.$this->invoice->invoice_number.' - Slimme-PC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-invoice',
            with: ['invoice' => $this->invoice],
        );
    }

    public function attachments(): array
    {
        if ($this->invoice->pdf_path && Storage::disk('local')->exists($this->invoice->pdf_path)) {
            return [
                Attachment::fromPath(Storage::disk('local')->path($this->invoice->pdf_path))
                    ->as($this->invoice->invoice_number.'.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
