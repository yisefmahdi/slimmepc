<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewCustomerMail extends Mailable
{
    use Queueable;

    public function __construct(
        public User $user
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nieuwe klant toegevoegd: ' . $this->user->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-customer',
            with: ['user' => $this->user],
        );
    }
}
