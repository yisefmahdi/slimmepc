<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Make externally-supplied filenames (IMAP attachment names, chunk-upload
 * client names, …) safe to store on the local disk.
 *
 * Attacker-controlled names may contain directory separators, traversal
 * ("../../.env"), NUL bytes, Windows drive prefixes or shell metacharacters.
 * This helper reduces any input to a flat, innocuous basename and falls back
 * to a random name when nothing usable remains.
 */
class SafeFilename
{
    public const MAX_LENGTH = 180;

    /**
     * Sanitize an externally-supplied filename (e.g. an e-mail attachment
     * name). Never returns a string containing "/" or "\" or "..".
     */
    public static function fromExternal(string $raw): string
    {
        // Strip NUL bytes first (they truncate paths in some libc calls).
        $raw = str_replace("\0", '', $raw);

        // Normalize Windows separators, then take the final path segment.
        $raw = str_replace('\\', '/', $raw);
        $base = basename($raw);

        // MIME-decoded leftovers such as quotes around the name.
        $base = trim($base, " \t\n\r\x0B\"'");

        // Collapse any remaining dot-runs ("....") and strip leading dots
        // (hidden files like ".htaccess" must never be recreated).
        $base = (string) preg_replace('/\.+/', '.', $base);
        $base = ltrim($base, '.');

        // Keep only innocuous characters; everything else becomes "_".
        // Unicode letters/numbers are preserved for Arabic/Dutch filenames.
        $base = (string) preg_replace('/[^A-Za-z0-9_\-\. \pL\pN]/u', '_', $base);
        $base = trim(preg_replace('/\s+/', ' ', $base) ?? '');

        // Enforce a sane length while keeping the extension readable.
        if (mb_strlen($base) > self::MAX_LENGTH) {
            $ext = pathinfo($base, PATHINFO_EXTENSION);
            $ext = $ext !== '' ? '.' . mb_substr($ext, 0, 10) : '';
            $base = mb_substr(pathinfo($base, PATHINFO_FILENAME), 0, self::MAX_LENGTH - mb_strlen($ext)) . $ext;
            $base = trim($base);
        }

        if ($base === '' || $base === '.' || mb_strtolower($base) === 'null') {
            $base = 'bijlage-' . Str::random(8);
        }

        // Paranoia: never hand back a separator or traversal, whatever the input.
        if (str_contains($base, '/') || str_contains($base, '\\') || $base === '..' || str_contains($base, '..')) {
            $ext = pathinfo($base, PATHINFO_EXTENSION);
            $base = 'bijlage-' . Str::random(8) . ($ext !== '' ? '.' . preg_replace('/[^A-Za-z0-9]/', '', $ext) : '');
        }

        return $base;
    }
}
