<x-admin.layout title="Notificaties">
    <div class="mx-auto max-w-5xl space-y-6">
        {{-- Status card --}}
        <div class="rounded-2xl border p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold" style="color: var(--c-heading)">Pushmeldingen</h2>
                    <p class="mt-1 text-sm" style="color: var(--c-muted)" id="pushStatusText">
                        Bij elke nieuwe aanvraag, chat of bestelling ontvang je direct een melding op dit apparaat — ook als de e-mail nog niet is geopend.
                    </p>
                    <p class="mt-2 hidden text-sm font-semibold text-amber-600" id="pushConfigWarning" @if($firebaseReady) style="display:none" @endif>
                        Firebase is nog niet ingesteld (FIREBASE_API_KEY / APP_ID ontbreken in .env). Meldingen werken pas na de setup.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" id="pushEnableBtn" data-loading
                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-blue-700">
                        Notificaties inschakelen
                    </button>
                    <button type="button" id="pushTestBtn" data-loading
                        class="rounded-xl border px-5 py-2.5 text-sm font-bold" style="color: var(--c-heading); border-color: rgba(148,163,184,.35)">
                        Testmelding
                    </button>
                </div>
            </div>
        </div>

        {{-- My devices --}}
        <div class="rounded-2xl border p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <h3 class="text-base font-extrabold" style="color: var(--c-heading)">Mijn apparaten ({{ $tokens->count() }})</h3>
            @if($tokens->isEmpty())
                <p class="mt-2 text-sm" style="color: var(--c-muted)">Nog geen apparaat geregistreerd. Klik hierboven op “Notificaties inschakelen”.</p>
            @else
                <div class="mt-4 space-y-2" id="pushDeviceList">
                    @foreach($tokens as $token)
                        <div class="flex items-center justify-between gap-3 rounded-xl border px-4 py-3" style="border-color: rgba(148,163,184,.2)">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-bold" style="color: var(--c-heading)">
                                    {{ $token->device_label ?: ucfirst($token->platform) }}
                                    <span class="ml-2 rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-blue-700">{{ $token->platform }}</span>
                                </p>
                                <p class="text-xs" style="color: var(--c-muted)">
                                    Aangemeld {{ $token->created_at?->format('d-m-Y H:i') }}
                                    @if($token->last_used_at) · laatst gebruikt {{ $token->last_used_at->format('d-m-Y H:i') }} @endif
                                </p>
                            </div>
                            <button type="button" class="push-delete-btn rounded-lg border px-3 py-1.5 text-xs font-bold text-red-600"
                                style="border-color: rgba(248,113,113,.4)" data-id="{{ $token->id }}">
                                Verwijderen
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recent pushes --}}
        <div class="rounded-2xl border p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2)">
            <h3 class="text-base font-extrabold" style="color: var(--c-heading)">Recent verzonden</h3>
            @if($logs->isEmpty())
                <p class="mt-2 text-sm" style="color: var(--c-muted)">Nog geen meldingen verzonden.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-xs uppercase" style="color: var(--c-muted)">
                                <th class="px-3 py-2">Type</th>
                                <th class="px-3 py-2">Titel</th>
                                <th class="px-3 py-2">Bereikt</th>
                                <th class="px-3 py-2">Tijd</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr class="border-t" style="border-color: rgba(148,163,184,.15)">
                                    <td class="px-3 py-2 font-bold" style="color: var(--c-heading)">{{ $log->type }}{{ $log->ref_id ? ' · '.$log->ref_id : '' }}</td>
                                    <td class="px-3 py-2" style="color: var(--c-body)">{{ $log->title }}</td>
                                    <td class="px-3 py-2" style="color: var(--c-body)">{{ $log->delivered }}/{{ $log->targeted }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap" style="color: var(--c-muted)">{{ $log->created_at?->format('d-m-Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</x-admin.layout>
