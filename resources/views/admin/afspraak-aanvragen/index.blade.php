<x-admin.layout title="Afspraak aanvragen">
    <div class="inbox-app flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div id="afspraakPageHeader" class="mb-4 flex shrink-0 flex-wrap items-center justify-between gap-x-2 gap-y-2">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Afspraak aanvragen</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Aanvragen via het afspraak-aan-huis formulier.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="rounded-full bg-red-50 px-3 py-1.5 font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400" id="afspraakCountNew">Nieuw: 0</span>
                <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="afspraakCountTotal">Totaal: 0</span>
            </div>
        </div>

        {{-- Table card --}}
        <div class="inbox-pane flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border"
             style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2); box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06)">

            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148, 163, 184, 0.15)">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input type="text" id="afspraakSearch" placeholder="Zoek op naam, e-mail, apparaat, adres..."
                               class="form-input h-10 w-full pl-4 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="afspraakStatusFilter"
                                class="h-9 min-w-0 flex-1 rounded-lg border px-3 text-xs outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40 sm:flex-none"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="">Alle statussen</option>
                            <option value="new">Nieuw</option>
                            <option value="in_progress">In behandeling</option>
                            <option value="completed">Afgerond</option>
                        </select>
                        <select id="afspraakPerPage"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="inbox-scroll min-h-0 flex-1 overflow-auto" style="overflow:auto">
                <table class="w-full min-w-[780px] border-collapse text-left" style="min-width:780px">
                    <thead class="sticky top-0 z-10" style="background-color: var(--c-card); border-color: rgba(148,163,184,0.15)">
                        <tr class="border-b" style="border-color: rgba(148, 163, 184, 0.15)">
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Aanvraagnr</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Naam</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Apparaat</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Adres</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Status</th>
                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Ontvangen</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Actie</th>
                        </tr>
                    </thead>
                    <tbody id="afspraakTableBody"></tbody>
                </table>
            </div>

            <div id="afspraakPagination" class="inbox-pagination shrink-0 border-t px-3 py-2" style="border-color: rgba(148, 163, 184, 0.15)"></div>
        </div>
    </div>

    {{-- Detail modal --}}
    <x-admin.modal id="afspraakDetailModal" title="Afspraak details" size="lg">
        <div id="afspraakDetail" class="flex min-h-0 flex-col">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-x-3 gap-y-2.5">
                <div class="flex min-w-0 items-center gap-3">
                    <span id="afspraakAvatar" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)]">?</span>
                    <div class="min-w-0">
                        <p id="afspraakName" class="truncate font-extrabold" style="color: var(--c-heading)">—</p>
                        <p id="afspraakMeta" class="truncate text-xs" style="color: var(--c-muted)">—</p>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <select id="afspraakStatusSelect"
                            class="h-9 cursor-pointer appearance-none rounded-lg border py-1.5 pl-3 pr-8 text-xs font-bold leading-none outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 12px;">
                        <option value="new">Nieuw</option>
                        <option value="in_progress">In behandeling</option>
                        <option value="completed">Afgerond</option>
                    </select>
                    <button type="button" id="afspraakDeleteBtn" title="Verwijder"
                            class="rounded-lg p-2 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400" style="color: var(--c-muted)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148,163,184,0.15); color: var(--c-heading)">
                    Nummer: <span id="afspraakNumber" class="font-bold">—</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148,163,184,0.15); color: var(--c-heading)">
                    Tel: <span id="afspraakPhone" class="font-bold">—</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148,163,184,0.15); color: var(--c-heading)">
                    Gewenst: <span id="afspraakPreferred" class="font-bold">—</span>
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148,163,184,0.15); color: var(--c-heading)">
                    Ontvangen: <span id="afspraakDate" class="font-bold">—</span>
                </span>
            </div>

            <div id="afspraakFields" class="mt-4"></div>
        </div>
    </x-admin.modal>

    <x-admin.modal id="afspraakDeleteModal" title="Afspraak verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je
                    <span id="afspraakDeleteName" class="font-bold">deze aanvraag</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">
                    De volledige aanvraag wordt permanent verwijderd.
                </p>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
            <button type="button" id="afspraakDeleteConfirmBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] transition duration-300 hover:-translate-y-0.5 hover:bg-red-700">
                Ja, verwijderen
            </button>
        </x-slot>
    </x-admin.modal>

    <script src="{{ asset('assets/js/admin/afspraak-aanvragen.js') }}?v={{ filemtime(public_path('assets/js/admin/afspraak-aanvragen.js')) }}"></script>
</x-admin.layout>
