<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

/**
 * Interne cron-trigger voor hosting zonder echte cron/CLI (Plesk-chroot).
 * Alleen via lang geheim token; bedoeld voor een externe ping-service
 * (cron-job.org) of Plesk "Fetch a URL"-taak, elke 5 minuten.
 */
class InternalCronController extends Controller
{
    public function __invoke(string $token): JsonResponse
    {
        $expected = (string) config('app.cron_token', '');

        if ($expected === '' || ! hash_equals($expected, $token)) {
            abort(404);
        }

        $jobsBefore = (int) DB::table('jobs')->count();

        Artisan::call('schedule:run');

        Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--tries' => 3,
            '--max-time' => 50,
        ]);

        return response()->json([
            'ok' => true,
            'jobs_before' => $jobsBefore,
            'jobs_after' => (int) DB::table('jobs')->count(),
            'failed' => (int) DB::table('failed_jobs')->count(),
        ]);
    }
}
