<x-admin.layout title="Ontvangst — {{ ['laptop' => 'Laptops-PC', 'ipad_iphone' => 'iPad-iPhone', 'playstation_xbox' => 'PlayStation-Xbox'][$type] ?? $type }}">
    @php
        $typeLabel = ['laptop' => 'Laptops-PC', 'ipad_iphone' => 'iPad-iPhone', 'playstation_xbox' => 'PlayStation-Xbox'][$type] ?? $type;
        $typeParam = $type;
    @endphp
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Ontvangst — {{ $typeLabel }}</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Apparaten ontvangst bevestigingen — {{ $typeLabel }}.</p>
            </div>
            <a href="{{ route('admin.bevestiging-mail.ontvangst.create', ['type' => $typeParam]) }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m15-7.5H12m0 0V4.5m0 15V12m0 0H4.5m15 0H12" />
                </svg>
                Nieuwe ontvangst
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input type="text" id="ontvangstSearch" placeholder="Zoek op naam, e-mail, T-nummer, apparaattype..."
                               class="form-input h-10 w-full pl-4 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="ontvangstPerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[1100px] border-collapse text-left" style="min-width:1100px">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam klant</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">E-mail</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">T-nummer</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Apparaattype</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Serienummer</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Datum ontvangst</th>
                            <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="ontvangstTableBody"></tbody>
                </table>
            </div>
            <div id="ontvangstPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Preview modal --}}
    <x-admin.modal id="ontvangstPreviewModal" title="Ontvangst details" size="md">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Naam klant</p>
                    <p id="prevName" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">E-mailadres</p>
                    <p id="prevEmail" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">T-nummer</p>
                    <p id="prevTNum" class="text-sm font-bold text-blue-600">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Apparaat</p>
                    <p id="prevDevice" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Serienummer</p>
                    <p id="prevSerial" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Datum ontvangst</p>
                    <p id="prevDate" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
            </div>
            <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Opmerkingen</p>
                <p id="prevNotes" class="text-sm" style="color: var(--c-heading)">—</p>
            </div>
            <div class="mt-6 p-4 rounded-2xl border bg-blue-50/50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-900/30">
                <label class="block text-xs font-bold text-blue-700 dark:text-blue-300 mb-2 uppercase">Status wijzigen</label>
                <div class="flex gap-2">
                    <select id="prevStatus" class="flex-1 h-10 rounded-xl border px-3 text-sm outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="received">Ontvangen</option>
                        <option value="processing">In behandeling</option>
                        <option value="completed">Voltooid</option>
                    </select>
                    <button type="button" id="prevStatusUpdateBtn" class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-bold text-white hover:bg-blue-700 shadow-sm">
                        Opslaan
                    </button>
                </div>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Sluiten</button>
        </x-slot>
    </x-admin.modal>

    {{-- Confirm Status Modal --}}
    <x-admin.modal id="ontvangstConfirmStatusModal" title="Status bevestigen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat de reparatie is voltooid?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Er wordt een bevestigingsmail naar de klant gestuurd.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="ontvangstConfirmStatusBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] hover:bg-blue-700">Ja, bevestigen</button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete modal --}}
    <x-admin.modal id="ontvangstDeleteModal" title="Ontvangst verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="ontvangstDeleteName" class="font-bold">deze ontvangst</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze ontvangst wordt permanent verwijderd.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="ontvangstDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,.25)] hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script>
        window.ONTVANGST_TYPE = @json($typeParam);
    </script>
    <script src="{{ asset('assets/js/admin/device-receipts.js') }}?v={{ filemtime(public_path('assets/js/admin/device-receipts.js')) }}"></script>
</x-admin.layout>
