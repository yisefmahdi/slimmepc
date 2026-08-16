<?php

namespace App\Console\Commands;

use App\Models\ContactReply;
use App\Models\ContactSubmission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Webklex\PHPIMAP\Client;
use Webklex\PHPIMAP\ClientManager;

class FetchInboundContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contact:fetch-inbound
                           {--limit=50 : Maximum number of unseen messages to process per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch customer e-mail replies and attach them to their inbox threads';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $username = config('contact-inbox.imap.username');
        $password = config('contact-inbox.imap.password');

        if (! $username || ! $password) {
            $this->error('IMAP credentials are not configured (CONTACT_IMAP_USERNAME / CONTACT_IMAP_PASSWORD).');

            return self::FAILURE;
        }

        try {
            $client = $this->connect();
        } catch (\Exception $e) {
            $this->error('IMAP connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->line('Connected to '.$username);

        $limit = max(1, min((int) $this->option('limit'), 200));

        try {
            $messages = $client->getFolderByPath(config('contact-inbox.mailbox'))
                ->query()
                ->unseen()
                ->leaveUnread()
                ->setFetchOrder('asc')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            $this->error('Failed to fetch messages: '.$e->getMessage());
            $client->disconnect();

            return self::FAILURE;
        }

        $processed = 0;
        $matched = 0;

        foreach ($messages as $message) {
            $processed++;

            $token = $this->findReplyToken($message);
            $submission = $token ? ContactSubmission::find($token) : null;

            if (! $submission) {
                $this->warn(sprintf(
                    'Unmatched reply from "%s" (token: %s) — left unread.',
                    $this->senderOf($message),
                    $token ?? 'none',
                ));

                continue;
            }

            $this->appendReply($message, $submission);

            try {
                $message->setFlag('Seen');
            } catch (\Exception $e) {
                $this->warn('Could not mark message as seen: '.$e->getMessage());
            }

            $matched++;

            $this->info(sprintf(
                'Added reply from "%s" to submission #%d.',
                $this->senderOf($message),
                $submission->id,
            ));
        }

        $client->disconnect();

        $this->line("Done. {$matched} of {$processed} messages matched.");

        return self::SUCCESS;
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

        foreach ($message->getTo() as $address) {
            $needles[] = $address->mail;
        }

        $deliveredTo = $message->getHeader('Delivered-To');

        if (is_object($deliveredTo)) {
            $deliveredTo = method_exists($deliveredTo, 'toString')
                ? $deliveredTo->toString()
                : (method_exists($deliveredTo, 'jsonSerialize') ? json_encode($deliveredTo) : null);
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
                    $this->warn('Could not save attachment: '.$e->getMessage());
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
        foreach ($message->getFrom() as $address) {
            return $address->personal ?: $address->mail;
        }

        return 'onbekend';
    }
}