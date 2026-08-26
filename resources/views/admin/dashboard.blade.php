<x-admin.layout title="Dashboard">
    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-5">
        <x-admin.stat-card label="Klanten" :value="$stats['customers']">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card label="Techniciens" :value="$stats['technicians']">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" />
                </svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card label="Bestellingen" value="{{ $stats['orders'] }}">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card label="Reparaties" value="{{ $stats['repairs'] }}">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788" />
                </svg>
            </x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card label="Contactaanvragen" :value="$stats['contact_new']">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                </svg>
            </x-slot>
        </x-admin.stat-card>
    </div>

    {{-- Welcome banner --}}
    <div class="mt-6 overflow-hidden rounded-2xl border bg-gradient-to-r from-[#075be8] to-[#064bd7] p-6 text-white shadow-[0_12px_25px_rgba(0,91,234,0.25)] sm:p-8 fade-in-up" style="animation-delay: 120ms">
        <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl">Welkom terug, {{ Auth::user()->name }}!</h2>
                <p class="mt-1 text-sm text-blue-100">
                    Beheer je klanten, bestellingen en reparaties vanaf één plek.
                </p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold backdrop-blur">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-400"></span>
                </span>
                Systeem online
            </span>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2">
            <x-admin.card title="Recente reparatie-aanmeldingen">
                <x-slot name="action">
                    <a href="{{ route('admin.reparatie-aanmeldingen.index') }}" class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Alles bekijken</a>
                </x-slot>
                <div class="overflow-x-auto -m-6">
                    <table class="w-full min-w-[640px] text-start text-sm">
                        <thead>
                            <tr style="color: var(--c-muted)">
                                <th class="px-6 py-3 text-start text-xs font-bold uppercase tracking-wider">Nummer</th>
                                <th class="px-6 py-3 text-start text-xs font-bold uppercase tracking-wider">Klant</th>
                                <th class="px-6 py-3 text-start text-xs font-bold uppercase tracking-wider">Apparaat</th>
                                <th class="px-6 py-3 text-start text-xs font-bold uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-start text-xs font-bold uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentRepairs as $r)
                                <tr class="border-t" style="border-color: rgba(148,163,184,0.12)">
                                    <td class="px-6 py-3 font-semibold" style="color: var(--c-heading)">{{ $r->repair_number }}</td>
                                    <td class="px-6 py-3">
                                        <div class="font-semibold" style="color: var(--c-heading)">{{ $r->name }}</div>
                                        <div class="text-xs" style="color: var(--c-muted)">{{ $r->email }}</div>
                                    </td>
                                    <td class="px-6 py-3" style="color: var(--c-heading)">{{ $r->brand }} {{ $r->model }}
                                        <div class="text-xs" style="color: var(--c-muted)">{{ $r->device }}</div>
                                    </td>
                                    <td class="px-6 py-3" style="color: var(--c-muted)">{{ $r->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="px-6 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $r->status === 'new' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : ($r->status === 'in_progress' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300') }}">
                                            {{ $r->status === 'new' ? 'Nieuw' : ($r->status === 'in_progress' ? 'In behandeling' : 'Afgerond') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788" />
                                                </svg>
                                            </span>
                                            <p class="font-semibold" style="color: var(--c-heading)">Nog geen reparatie-aanmeldingen</p>
                                            <p class="text-xs" style="color: var(--c-muted)">Aanmeldingen via het formulier verschijnen hier.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Snelle acties">
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold" style="color: var(--c-heading)">Nieuwe klant</span>
                    </a>

                    <a href="#" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold" style="color: var(--c-heading)">Nieuwe reparatie</span>
                    </a>

                    <a href="#" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold" style="color: var(--c-heading)">Geschiedenis</span>
                    </a>

                    <a href="#" class="group flex flex-col items-center gap-2 rounded-xl border border-dashed p-4 text-center transition hover:border-blue-400 hover:bg-blue-50/50 dark:hover:bg-blue-900/20" style="border-color: rgba(148, 163, 184, 0.3)">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:scale-105 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        <span class="text-xs font-semibold" style="color: var(--c-heading)">Goedkeuringen</span>
                    </a>
                </div>
            </x-admin.card>

            <x-admin.card title="Systeemstatus">
                <div class="space-y-4">
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="font-semibold" style="color: var(--c-heading)">Database</span>
                            <span class="inline-flex items-center gap-1 font-bold text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Actief
                            </span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full w-full rounded-full bg-gradient-to-r from-[#075be8] to-[#064bd7]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="font-semibold" style="color: var(--c-heading)">Wachtrij</span>
                            <span class="inline-flex items-center gap-1 font-bold text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Actief
                            </span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full w-full rounded-full bg-gradient-to-r from-[#075be8] to-[#064bd7]"></div>
                        </div>
                    </div>

                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-xs">
                            <span class="font-semibold" style="color: var(--c-heading)">Cache</span>
                            <span class="inline-flex items-center gap-1 font-bold text-green-600 dark:text-green-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-3.5 w-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                                Actief
                            </span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                            <div class="h-full w-full rounded-full bg-gradient-to-r from-[#075be8] to-[#064bd7]"></div>
                        </div>
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</x-admin-layout>

