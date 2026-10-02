<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPushLog;
use App\Models\FcmToken;
use App\Services\AdminPushNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PushController extends Controller
{
    /**
     * My devices + recent push log.
     */
    public function index(): View
    {
        return view('admin.notificaties.index', [
            'tokens' => FcmToken::where('user_id', auth()->id())->latest()->get(),
            'logs' => AdminPushLog::latest()->limit(20)->get(['type', 'ref_id', 'title', 'targeted', 'delivered', 'created_at']),
            'firebaseReady' => (string) config('firebase.web.api_key', '') !== ''
                && (string) config('firebase.web.app_id', '') !== '',
        ]);
    }

    /**
     * Register (or refresh) one device token for the logged-in admin.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:512'],
            'platform' => ['nullable', 'string', 'in:web,android,ios'],
            'device_label' => ['nullable', 'string', 'max:255'],
        ], [], [
            'token' => 'token',
        ]);

        FcmToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id' => auth()->id(),
                'platform' => $data['platform'] ?? 'web',
                'device_label' => $data['device_label'] ?? substr((string) $request->userAgent(), 0, 255),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['message' => 'Notificaties ingeschakeld op dit apparaat.']);
    }

    /**
     * Remove one of MY devices (never another admin's).
     */
    public function destroy(FcmToken $fcmToken): JsonResponse
    {
        if ((int) $fcmToken->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $fcmToken->delete();

        return response()->json(['message' => 'Apparaat verwijderd.']);
    }

    /**
     * Test push to MY devices only.
     */
    public function test(): JsonResponse
    {
        $count = FcmToken::where('user_id', auth()->id())->count();

        if ($count === 0) {
            return response()->json(['message' => 'Geen apparaat geregistreerd. Schakel eerst notificaties in.'], 422);
        }

        AdminPushNotifier::notify(
            'test',
            (string) auth()->id(),
            'Testmelding Slimme-PC',
            'Druk op deze melding om het dashboard te openen.',
            route('admin.dashboard', absolute: true)
        );

        return response()->json(['message' => 'Testmelding verzonden naar '.$count.' apparaat/apparaten.']);
    }
}
