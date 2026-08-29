<x-admin.layout title="Hardware Facturen">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Hardware — Handmatige facturen</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Facturen aanmaken en verzenden naar klanten.</p>
            </div>
            <a href="{{ route('admin.bevestiging-mail.hardware.create') }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m15-7.5H12m0 0V4.5m0 15V12m0 0H4.5m15 0H12" />
                </svg>
                Nieuwe factuur
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input type="text" id="hardwareSearch" placeholder="Zoek op naam, e-mail, factuurnummer, apparaat..."
                               class="form-input h-10 w-full pl-4 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="hardwarePerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto">
                <table class="w-full min-w-[1280px] border-collapse text-left" style="min-width:1280px">
                    <thead class="sticky top-0 z-10" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Datum</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Order ID</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Naam klant</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">E-mailadres</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Apparaat info</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Probleembeschrijving</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Subtotaal</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Totaal</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Download</th>
                            <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Actie</th>
                        </tr>
                    </thead>
                    <tbody id="hardwareTableBody"></tbody>
                </table>
            </div>
            <div id="hardwarePagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Delete modal --}}
    <x-admin.modal id="hardwareDeleteModal" title="Factuur verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="hardwareDeleteName" class="font-bold">deze factuur</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">De factuur en het PDF-bestand worden permanent verwijderd.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="hardwareDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,.25)] hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script src="{{ asset('assets/js/admin/hardware-invoices.js') }}?v={{ filemtime(public_path('assets/js/admin/hardware-invoices.js')) }}"></script>
</x-admin.layout>
