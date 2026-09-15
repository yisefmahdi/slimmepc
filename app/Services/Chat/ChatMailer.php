<?php

namespace App\Services\Chat;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Eén mail versturen zonder dat een haperende SMTP-verbinding (DNS/timeout)
 * de andere mails in dezelfde afterResponse-callback meeneemt.
 * Eén retry bij transportfouten, daarna alleen loggen — nooit gooien.
 */
class ChatMailer
{
    public static function send(string $email, Mailable $mail, ?string $context = null): bool
    {
        $label = $context ? " ({$context})" : '';

        try {
            Mail::to($email)->send($mail);

            return true;
        } catch (TransportExceptionInterface $e) {
            Log::warning('[chat-mail] transport mislukt'.$label.', retry: '.$e->getMessage());
        } catch (\Throwable $e) {
            Log::warning('[chat-mail] mislukt'.$label.': '.$e->getMessage());

            return false;
        }

        sleep(3);

        try {
            Mail::to($email)->send($mail);

            return true;
        } catch (\Throwable $e) {
            Log::warning('[chat-mail] retry mislukt'.$label.': '.$e->getMessage());

            return false;
        }
    }
}
