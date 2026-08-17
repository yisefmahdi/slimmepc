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
     * Strip the quoted original message + signature junk that e-mail clients
     * append below the actual reply, so the stored body is just what the
     * customer typed. Also used to clean up already-stored rows.
     */
    public function cleanBody(string $body): string
    {
        // Some e-mail clients ship broken byte sequences (e.g. a Latin-1 byte
        // glued to a UTF-8 char) that MySQL would reject. Scrub them into
        // valid UTF-8 (invalid bytes become U+FFFD) before touching anything.
        $body = mb_scrub($body, 'UTF-8');

        // NOTE: the "u" modifier is required — without it PCRE splits \R on
        // the byte 0x85 (NEL), which is also a valid continuation byte inside
        // multibyte UTF-8 chars (Arabic etc.), corrupting them mid-character.
        $lines = preg_split('/\R/u', $body);

        if (! is_array($lines)) {
            return trim($body);
        }

        $out = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') {
                if ($out && end($out) !== '') {
                    $out[] = '';
                }

                continue;
            }

            // Gmail quotes the original below a "…wrote:" line, prefixing each
            // line with ">". Stop at the first quoted line or that separator.
            if (str_starts_with($trimmed, '>')) {
                break;
            }

            if (str_contains($trimmed, 'تمت كتابة ما يلي بواسطة') || preg_match('/^On\s+.+\swrote:/i', $trimmed)) {
                break;
            }

            // Any language's quote separator ends with the sender address,
            // e.g. "… schreef slimmepc <yyyooo2004@gmail.com>:" (NL) or
            // "… <yyyooo2004@gmail.com> hat geschrieben:" (DE).
            if (preg_match('/@[^\s]+>:?$/', $trimmed)) {
                break;
            }

            // Gmail's "— Forwarded message —" block
            if ($trimmed === '— Forwarded message —' || $trimmed === '- Forwarded message -') {
                break;
            }

            $out[] = $line;
        }

        return trim(implode("\n", $out));
    }

    /**
     * Create the customer reply row (with attachments if present).
     *
     * If the e-mail carries attachments but none of them could be stored,
     * this throws BEFORE creating the reply row, so the message stays
     * unread and the next fetch pass retries it — a lost attachment can
     * never be silently burned again.
     */
    private function appendReply($message, ContactSubmission $submission): void
    {
        $body = $message->getTextBody();

        if (! $body && $html = $message->getHTMLBody()) {
            $body = trim(strip_tags($html));
        }

        $body = $this->cleanBody((string) $body);

        // Gmail's text/plain alternative replaces inline images with a
        // "[image: filename]" placeholder — drop it, the real file shows as
        // an attachment preview in the chat instead.
        $body = preg_replace('/\[image:\s*[^\]]+\]/i', '', $body) ?? $body;

        $attachments = $message->getAttachments();

        $savedName = $attachments->count() ? $this->storeFirstAttachment($attachments, $submission) : null;

        $reply = ContactReply::create([
            'contact_submission_id' => $submission->id,
            'sender' => 'customer',
            'body' => trim($body) ?: '(Geen tekst)',
            'attachment' => $savedName,
            'source' => 'inbound',
        ]);

        $submission->update(['status' => 'in_progress']);
    }

    /**
     * Persist the first storable attachment of the message.
     *
     * @param  iterable<int, \Webklex\PHPIMAP\Attachment>  $attachments
     *
     * @throws \RuntimeException when an attachment was expected but none could be saved
     */
    private function storeFirstAttachment(iterable $attachments, ContactSubmission $submission): ?string
    {
        $dir = 'contact/'.$submission->id.'/inbound';

        // webklex writes with raw file_put_contents(), which does NOT create
        // directories — without this the save fails silently. Storage::put()
        // creates the path itself, the makeDirectory is belt-and-suspenders.
        Storage::disk('local')->makeDirectory($dir);

        // Prefer real attached files (Content-Disposition: attachment) over
        // inline images: a follow-up e-mail that quotes the previous message
        // re-sends the old inline image as a MIME part, which would otherwise
        // show up as a "duplicate" of the first screenshot.
        $list = is_array($attachments) ? $attachments : iterator_to_array($attachments, false);

        usort($list, fn ($a, $b) => (int) ($a->getDisposition() !== 'attachment') - (int) ($b->getDisposition() !== 'attachment'));

        foreach ($list as $attachment) {
            try {
                $rawName = $attachment->getName();

                if ($rawName === '') {
                    continue;
                }

                $name = $attachment->decodeName($rawName);

                $content = $attachment->getContent();

                if ($content === '' || $content === false) {
                    continue;
                }

                Storage::disk('local')->put($dir.'/'.$name, $content);

                return 'inbound/'.$name;
            } catch (\Exception $e) {
                Log::warning('[contact-inbox] Could not save attachment: '.$e->getMessage());
            }
        }

        throw new \RuntimeException('All attachments failed to save.');
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
