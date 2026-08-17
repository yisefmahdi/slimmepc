<?php

namespace App\Services;

use App\Models\ContactReply;
use App\Models\ContactSubmission;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

/**
 * Pulls customer e-mail replies from the inbox over IMAP and attaches them
 * to their chat threads. Used by the admin middleware (once a minute), the
 * inbox page sync endpoint (every ~15s while open) and the manual
 * `contact:fetch-inbound` command. There is no background job / cron for
 * this — it runs on demand, so it can never "die" under load.
 */
class InboundContactFetcher
{
    /**
     * Run one fetch pass.
     *
     * @return array{processed:int, matched:int, errors:string[]}
     */
    public function run(?int $limit = 30): array
    {
        $processed = 0;
        $matched = 0;
        $errors = [];

        if (! config('contact-inbox.imap.username') || ! config('contact-inbox.imap.password')) {
            $errors[] = 'IMAP credentials are not configured (CONTACT_IMAP_USERNAME / CONTACT_IMAP_PASSWORD).';

            return compact('processed', 'matched', 'errors');
        }

        try {
            $client = $this->connect();
        } catch (\Exception $e) {
            $errors[] = 'IMAP connection failed: '.$e->getMessage();
            Log::warning('[contact-inbox] '.end($errors));

            return compact('processed', 'matched', 'errors');
        }

        try {
            $messages = $client->getFolderByPath(config('contact-inbox.mailbox'))
                ->query()
                ->unseen()
                ->to('+reply-')
                ->leaveUnread()
                ->setFetchOrder('asc')
                ->limit(max(1, min((int) $limit, 200)))
                ->get();
        } catch (\Exception $e) {
            $errors[] = 'Failed to fetch messages: '.$e->getMessage();
            Log::warning('[contact-inbox] '.end($errors));
            $client->disconnect();

            return compact('processed', 'matched', 'errors');
        }

        foreach ($messages as $message) {
            $processed++;

            $token = $this->findReplyToken($message);
            $submission = $token ? ContactSubmission::find($token) : null;

            if (! $submission) {
                $errors[] = sprintf(
                    'Unmatched reply from "%s" (token: %s) — left unread.',
                    $this->senderOf($message),
                    $token ?? 'none',
                );
                Log::warning('[contact-inbox] '.end($errors));

                continue;
            }

            try {
                $this->appendReply($message, $submission);
                $message->setFlag('Seen');
                $matched++;
            } catch (\Exception $e) {
                $errors[] = 'Could not process reply for submission #'.$submission->id.': '.$e->getMessage();
                Log::warning('[contact-inbox] '.end($errors));
            }
        }

        $client->disconnect();

        return compact('processed', 'matched', 'errors');
    }

    /**
     * Establish the IMAP connection.
     */
    private function connect(): Client
    {
        $manager = new ClientManager([
            'options' => [
                'validate_cert' => (bool) config('contact-inbox.imap.validate_cert'),
                'debug' => false,
            ],
            'accounts' => [
                'default' => [
                    'host' => config('contact-inbox.imap.host'),
                    'port' => config('contact-inbox.imap.port'),
                    'encryption' => config('contact-inbox.imap.encryption'),
                    'validate_cert' => (bool) config('contact-inbox.imap.validate_cert'),
                    'username' => config('contact-inbox.imap.username'),
                    'password' => config('contact-inbox.imap.password'),
                    'protocol' => 'imap',
                ],
            ],
        ]);

        return $manager->account('default');
    }

    /**
     * Extract the "+reply-{id}" token from the message headers.
     */
    private function findReplyToken($message): ?int
    {
        $needles = [];

        $to = $message->getTo();

        if (is_object($to) && method_exists($to, 'all')) {
            foreach ($to->all() as $address) {
                if (is_object($address) && isset($address->mail)) {
                    $needles[] = $address->mail;
                }
            }
        } else {
            foreach ($to as $address) {
                $needles[] = $address->mail;
            }
        }

        $deliveredTo = $message->getHeader('Delivered-To');

        if (is_object($deliveredTo)) {
            $deliveredTo = $deliveredTo->raw ?? json_encode($deliveredTo);
        }

        if ($deliveredTo) {
            $needles[] = (string) $deliveredTo;
        }

        foreach ($needles as $needle) {
            if (preg_match('/\+reply-(\d+)/i', (string) $needle, $m)) {
                return (int) $m[1];
            }
        }

        return null;
    }

    /**
     * Create the customer reply row (with attachments if present).
     */
    private function appendReply($message, ContactSubmission $submission): void
    {
        $body = $message->getTextBody();

        if (! $body && $html = $message->getHTMLBody()) {
            $body = trim(strip_tags($html));
        }

        $reply = ContactReply::create([
            'contact_submission_id' => $submission->id,
            'sender' => 'customer',
            'body' => trim($body) ?: '(Geen tekst)',
            'source' => 'inbound',
        ]);

        $attachments = $message->getAttachments();

        if ($attachments->count()) {
            $dir = storage_path('app/private/contact/'.$submission->id.'/inbound');

            foreach ($attachments as $attachment) {
                try {
                    $name = $attachment->getName();

                    $attachment->save($dir, $name, true);

                    $reply->update(['attachment' => 'inbound/'.$name]);

                    break;
                } catch (\Exception $e) {
                    Log::warning('[contact-inbox] Could not save attachment: '.$e->getMessage());
                }
            }
        }

        $submission->update(['status' => 'in_progress']);
    }

    /**
     * Best-effort sender description.
     */
    private function senderOf($message): string
    {
        $from = $message->getFrom();

        if (is_object($from) && method_exists($from, 'first')) {
            $address = $from->first();

            if ($address) {
                return $address->personal ?: $address->mail;
            }
        } else {
            foreach ($from as $address) {
                return $address->personal ?: $address->mail;
            }
        }

        return 'onbekend';
    }
}
