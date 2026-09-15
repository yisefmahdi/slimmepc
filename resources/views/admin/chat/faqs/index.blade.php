<x-admin.layout title="Kennisbank — Live Chat">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Kennisbank</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Vragen &amp; antwoorden waarmee de AI-chat antwoordt. Alleen actieve items worden gebruikt.</p>
            </div>
            <button type="button" id="faqCreateBtn"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m15-7.5H12m0 0V4.5m0 15V12m0 0H4.5m15 0H12" />
                </svg>
                Nieuwe vraag
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input type="text" id="faqSearch" placeholder="Zoek op vraag, antwoord of categorie..."
                               class="form-input h-10 w-full pl-4 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="faqStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="">Alle</option>
                        <option value="active">Actief</option>
                        <option value="inactive">Inactief</option>
                    </select>
                    <select id="faqPerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <p class="mt-2 text-xs" style="color: var(--c-muted)"><span id="faqCounts" class="font-bold">—</span></p>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width:900px">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Vraag</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Categorie</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Volgorde</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="faqTableBody"></tbody>
                </table>
            </div>
            <div id="faqPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Create/Edit modal --}}
    <x-admin.modal id="faqFormModal" title="Vraag" size="md">
        <form id="faqForm" class="space-y-4">
            <input type="hidden" id="faqId" value="">
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Vraag *</label>
                <input type="text" id="faqQuestion" maxlength="500" placeholder="Bijv. Wat kost een laptop reparatie?"
                       class="form-input h-11 w-full text-sm">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Antwoord *</label>
                <textarea id="faqAnswer" rows="4" maxlength="5000" placeholder="Het antwoord dat de AI geeft..."
                          class="form-input w-full py-3 text-sm"></textarea>
            </div>
            <div>
                <div class="mb-1.5 flex items-center justify-between gap-2">
                    <label class="block text-sm font-semibold" style="color: var(--c-heading)">Zoekwoorden <span class="font-normal" style="color: var(--c-muted)">(komma-gescheiden, NL + AR — hiermee vindt de AI deze vraag)</span></label>
                    <button type="button" id="faqKeywordsAiBtn"
                            class="inline-flex h-8 shrink-0 items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-700 transition hover:bg-blue-100 disabled:opacity-60">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" /></svg>
                        <span id="faqKeywordsAiLabel">Genereer met AI</span>
                    </button>
                </div>
                <input type="text" id="faqKeywords" maxlength="2000" placeholder="Bijv. telefoon, nummer, تواصل, اتصل"
                       class="form-input h-11 w-full text-sm">
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Categorie</label>
                    <input type="text" id="faqCategory" maxlength="100" placeholder="Bijv. Prijzen"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Volgorde</label>
                    <input type="number" id="faqSort" min="0" max="9999" value="0"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex cursor-pointer items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <input type="checkbox" id="faqActive" checked class="h-5 w-5 rounded accent-blue-600">
                        Actief
                    </label>
                </div>
            </div>
        </form>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="faqSaveBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] hover:bg-blue-700">Opslaan</button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete modal --}}
    <x-admin.modal id="faqDeleteModal" title="Vraag verwijderen" size="sm">
        <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="faqDeleteName" class="font-bold">deze vraag</span> wilt verwijderen?</p>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="faqDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,.25)] hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    <script src="{{ asset('assets/js/admin/chat-faqs.js') }}?v={{ filemtime(public_path('assets/js/admin/chat-faqs.js')) }}"></script>
</x-admin.layout>
