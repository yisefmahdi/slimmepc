<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Auditlog voor elke verzonden e-mail (mailinglijst + mass-email).
 *
 * Schrijft naar storage/logs/mail-send.log (channel "mailsend", géén .env nodig):
 * tijd, referentie, ontvanger, onderwerp, resultaat, duur. Inspecteerbaar via
 * File Manager; referentie + tijdstip zijn genoeg voor de hoster om een bericht
 * in de relay-logs terug te vinden.
 */
class MailAudit
{
    public static function sent(string $source, string $recipient, string $subject, float $seconds, ?int $attempt = null): string
    {
        $ref = (string) Str::uuid();

        Log::channel('mailsend')->info("[{$source}] ACCEPTED ref={$ref} to={$recipient} subject=".self::short($subject).' duration_ms='.(int) round($seconds * 1000).($attempt ? " attempt={$attempt}" : ''));

        return $ref;
    }

    public static function failed(string $source, string $recipient, string $subject, string $error, ?int $attempt = null): string
    {
        $ref = (string) Str::uuid();

        Log::channel('mailsend')->warning("[{$source}] FAILED ref={$ref} to={$recipient} subject=".self::short($subject).' error='.self::short($error, 200).($attempt ? " attempt={$attempt}" : ''));
        Log::warning("[{$source}] verzending mislukt voor {$recipient}: {$error}");

        return $ref;
    }

    private static function short(string $value, int $max = 120): string
    {
        $oneLine = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return mb_strlen($oneLine) > $max ? mb_substr($oneLine, 0, $max).'…' : $oneLine;
    }
}
