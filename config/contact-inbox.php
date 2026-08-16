<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Inbound e-mail (IMAP) — contact:fetch-inbound
    |--------------------------------------------------------------------------
    | Customer replies to the contact confirmation e-mail arrive on the
    | "+reply-{id}" alias in the same Gmail inbox. This command polls that
    | inbox over IMAP and appends each reply to its chat thread.
    |
    | Requires the IMAP setting "Enable IMAP" in Gmail and an App Password.
    */

    'imap' => [
        'host' => env('CONTACT_IMAP_HOST', 'imap.gmail.com'),
        'port' => (int) env('CONTACT_IMAP_PORT', 993),
        'encryption' => env('CONTACT_IMAP_ENCRYPTION', 'ssl'),
        'validate_cert' => (bool) env('CONTACT_IMAP_VALIDATE_CERT', true),
        'username' => env('CONTACT_IMAP_USERNAME', ''),
        'password' => env('CONTACT_IMAP_PASSWORD', ''),
    ],

    'mailbox' => env('CONTACT_IMAP_MAILBOX', 'INBOX'),

    /*
    |--------------------------------------------------------------------------
    | Admin notification e-mail
    |--------------------------------------------------------------------------
    | Every new contact submission triggers a notification to this address with
    | the submitter's details and a direct link to open the thread in the
    | admin dashboard. Falls back to MAIL_FROM_ADDRESS when unset.
    */

    'notify_email' => env('CONTACT_NOTIFY_EMAIL', env('MAIL_FROM_ADDRESS', '')),
];