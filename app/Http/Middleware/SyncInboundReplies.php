<?php

namespace App\Http\Middleware;

use App\Services\InboundContactFetcher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pulls inbound e-mail replies once a minute while the admin is active.
 *
 * Runs on every /admin request (after EnsureUserIsAdmin) but is throttled to
 * one fetch per 60 seconds and guarded with a lock, so it never hammers Gmail
 * and never blocks the dashboard when IMAP is slow or down.
 */
class SyncInboundReplies
{
    private const THROTTLE_SECONDS = 60;

    private const LOCK_KEY = 'contact:inbound-fetch-lock';

    private const LAST_KEY = 'contact:inbound-last-fetch';

    public function handle(Request $request, Closure $next): Response
    {
        $this->sync();

        return $next($request);
    }

    protected function sync(): void
    {
        $lock = Cache::lock(self::LOCK_KEY, 30);

        if (! $lock->get()) {
            return;
        }

        try {
            $last = Cache::get(self::LAST_KEY);

            if ($last && now()->getTimestamp() - (int) $last < self::THROTTLE_SECONDS) {
                return;
            }

            app(InboundContactFetcher::class)->run();

            Cache::put(self::LAST_KEY, now()->getTimestamp(), now()->addMinutes(10));
        } catch (\Throwable $e) {
            Log::warning('[contact-inbox] middleware sync failed: '.$e->getMessage());
        } finally {
            $lock->release();
        }
    }
}