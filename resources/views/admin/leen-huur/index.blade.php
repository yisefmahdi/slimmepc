<x-admin.layout title="Leen / Huur Laptop">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Leen / Huur Laptop</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Tijdelijke laptops voor klanten — uitgifte, retour en foto's.</p>
            </div>
            <a href="{{ route('admin.leen-huur.create') }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m15-7.5H12m0 0V4.5m0 15V12m0 0H4.5m15 0H12" />
                </svg>
                Laptop uitgeven
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input type="text" id="leenSearch" placeholder="Zoek op naam, e-mail, telefoon, laptop..."
                               class="form-input h-10 w-full pl-4 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="leenStatus"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="">Alle statussen</option>
                        <option value="uitgeleend">Uitgeleend</option>
                        <option value="teruggebracht">Teruggebracht</option>
                    </select>
                    <select id="leenPerPage"
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
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Leennummer</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam klant</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Telefoon</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Type laptop</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Datum uitgifte</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="leenTableBody"></tbody>
                </table>
            </div>
            <div id="leenPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Preview modal --}}
    <x-admin.modal id="leenPreviewModal" title="Uitgifte details" size="md">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Leennummer</p>
                    <p id="leenPrevNum" class="text-sm font-bold text-blue-600">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Status</p>
                    <p id="leenPrevStatus" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Naam klant</p>
                    <p id="leenPrevName" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">E-mailadres</p>
                    <p id="leenPrevEmail" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Telefoon</p>
                    <p id="leenPrevPhone" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Adres</p>
                    <p id="leenPrevAddress" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Reparatienummer</p>
                    <p id="leenPrevRepair" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Type laptop</p>
                    <p id="leenPrevLaptop" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
                <div class="p-3 rounded-xl border sm:col-span-2" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                    <p class="text-[10px] uppercase font-bold text-slate-500 mb-1">Datum uitgifte</p>
                    <p id="leenPrevDate" class="text-sm font-semibold" style="color: var(--c-heading)">—</p>
                </div>
            </div>
            <div class="p-3 rounded-xl border" style="background-color: var(--c-page); border-color: rgba(148,163,184,.2)">
                <div class="mb-2 flex items-center justify-between">
                    <p class="text-[10px] uppercase font-bold text-slate-500">Foto's</p>
                    <span id="leenPrevPhotosCount" class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">0</span>
                </div>
                <div id="leenPrevPhotosGrid" class="flex flex-wrap items-center gap-2"></div>
                <p id="leenPrevPhotosEmpty" class="text-xs" style="color: var(--c-muted)">Geen foto's bij deze uitgifte.</p>
            </div>
        </div>
        <x-slot name="footer">
            <a id="leenPrevPdf" href="#" target="_blank" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 text-sm font-semibold text-white hover:bg-slate-700">Overeenkomst (PDF)</a>
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Sluiten</button>
        </x-slot>
    </x-admin.modal>

    {{-- Photo lightbox --}}
    <x-admin.modal id="leenPhotoLightbox" title="Foto" size="lg">
        <div class="flex items-center justify-center rounded-xl bg-black/90 p-2">
            <img id="leenPhotoLightboxImg" src="" alt="Foto" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain">
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Sluiten</button>
        </x-slot>
    </x-admin.modal>

    {{-- Confirm retour modal --}}
    <x-admin.modal id="leenReturnModal" title="Laptop retour" subtitle="Bevestig dat de klant de laptop heeft teruggebracht." size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Is <span id="leenReturnName" class="font-bold">deze laptop</span> teruggebracht?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Er wordt een retourbevestiging per e-mail naar de klant gestuurd.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="leenReturnConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(5,150,105,.25)] hover:bg-emerald-700">Ja, retour bevestigen</button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete modal --}}
    <x-admin.modal id="leenDeleteModal" title="Uitgifte verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="leenDeleteName" class="font-bold">deze uitgifte</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze uitgifte wordt permanent verwijderd.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="leenDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,.25)] hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script src="{{ asset('assets/js/admin/laptop-loans.js') }}?v={{ filemtime(public_path('assets/js/admin/laptop-loans.js')) }}"></script>
</x-admin.layout>
