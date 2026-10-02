<?php

namespace App\Services;

use App\Models\AdminPushLog;
use App\Models\FcmToken;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\WebPushConfig;

/**
 * Central fan-out for admin push notifications (Firebase Cloud Messaging).
 *
 * Called right after every admin e-mail is sent, so the push arrives at the
 * same moment as the e-mail. Never throws — failures are logged and the
 * mail flow is never affected. Without Firebase credentials it degrades
 * gracefully (skipped + logged), exactly like the IMAP fetcher.
 */
class AdminPushNotifier
{
    /**
     * @return array{targeted: int, delivered: int}
     */
    public static function notify(string $type, ?string $refId, string $title, string $body, string $url): array
    {
        $result = ['targeted' => 0, 'delivered' => 0];

        try {
            if (! (bool) config('firebase.enabled', true)) {
                self::audit($type, $refId, $title, $url, $result, 'disabled: FIREBASE_ENABLED=false');

                return $result;
            }

            $credentials = (string) config('firebase.credentials', '');

            if ($credentials === '' || ! is_file($credentials)) {
                self::audit($type, $refId, $title, $url, $result, 'disabled: no service-account credentials');

                return $result;
            }

            // Only admins (role can change after token registration).
            $tokens = FcmToken::query()
                ->whereHas('user', fn ($q) => $q->where('role', 'admin'))
                ->pluck('token')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $result['targeted'] = count($tokens);

            if ($tokens === []) {
                self::audit($type, $refId, $title, $url, $result, 'skipped: no admin device tokens');

                return $result;
            }

            $message = CloudMessage::new()
                ->withNotification(Notification::create($title, $body))
                ->withData([
                    'url' => $url,
                    'type' => $type,
                    'ref' => (string) ($refId ?? ''),
                ])
                ->withWebPushConfig(WebPushConfig::fromArray([
                    'fcm_options' => ['link' => $url],
                ]));

            $messaging = (new Factory)->withServiceAccount($credentials)->createMessaging();
            $report = $messaging->sendMulticast($message, $tokens);

            $result['delivered'] = $report->successes()->count();

            // Drop dead tokens so future fan-outs stay clean.
            $dead = array_merge($report->unknownTokens(), $report->invalidTokens());

            if ($dead !== []) {
                FcmToken::whereIn('token', $dead)->delete();
            }

            $failed = $report->failures()->count();
            self::audit(
                $type, $refId, $title, $url, $result,
                $failed > 0 ? "sent with {$failed} failure(s), pruned ".count($dead).' dead token(s)' : 'sent'
            );

            FcmToken::whereIn('token', $report->validTokens())->update(['last_used_at' => now()]);
        } catch (\Throwable $e) {
            Log::warning('[admin-push] failed ('.$type.'/'.$refId.'): '.$e->getMessage());
            self::audit($type, $refId, $title, $url, $result, 'error: '.$e->getMessage());
        }

        return $result;
    }

    protected static function audit(
        string $type, ?string $refId, string $title, string $url, array $result, string $response
    ): void {
        try {
            AdminPushLog::create([
                'type' => $type,
                'ref_id' => $refId,
                'title' => mb_substr($title, 0, 255),
                'url' => mb_substr($url, 0, 1024),
                'targeted' => $result['targeted'],
                'delivered' => $result['delivered'],
                'response' => mb_substr($response, 0, 65535),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[admin-push] audit failed: '.$e->getMessage());
        }
    }
}
