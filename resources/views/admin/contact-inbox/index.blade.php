<x-admin.layout title="Inbox">
    {{-- App shell: fills the viewport below the admin header, no page scroll --}}
    <div class="inbox-app flex h-[calc(100dvh-6.5rem)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Slim header --}}
        <div class="mb-4 flex shrink-0 flex-wrap items-center justify-between gap-x-2 gap-y-2">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Inbox</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Contactaanvragen met chatgeschiedenis per bericht.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="rounded-full bg-red-50 px-3 py-1.5 font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400" id="inboxCountNew">
                    Nieuw: 0
                </span>
                <span class="hidden rounded-full bg-amber-50 px-3 py-1.5 font-bold text-amber-600 dark:bg-amber-900/30 dark:text-amber-400" id="inboxCountUnread">
                    Ongelezen: 0
                </span>
                <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="inboxCountTotal">
                    Totaal: 0
                </span>
            </div>
        </div>

        {{-- Two panes --}}
        <div class="inbox-panes grid min-h-0 flex-1 grid-cols-1 grid-rows-[minmax(0,1fr)] gap-4 overflow-hidden lg:grid-cols-[minmax(0,19rem)_minmax(0,1fr)]">

            {{-- ============ LEFT: conversation list ============ --}}
            <div id="inboxListPane"
                 class="inbox-pane flex min-h-0 flex-col overflow-hidden rounded-2xl border"
                 style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2); box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06)">

                {{-- Search --}}
                <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148, 163, 184, 0.15)">
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                        <input type="text" id="inboxSearch" placeholder="Zoek op naam, e-mail, telefoon of bericht..."
                               class="form-input h-10 pl-10 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <div class="mt-2.5 flex items-center gap-2">
                        <select id="inboxStatusFilter"
                                class="h-9 min-w-0 flex-1 rounded-lg border px-3 text-xs outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="">Alle statussen</option>
                            <option value="new">Nieuw</option>
                            <option value="in_progress">In behandeling</option>
                            <option value="replied">Beantwoord</option>
                            <option value="closed">Gesloten</option>
                        </select>
                        <select id="inboxPerPage"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>

                {{-- List (scrolls) --}}
                <div id="inboxList" class="inbox-scroll min-h-0 flex-1 overflow-y-auto px-2 py-2">
                    <div class="flex flex-col items-center gap-3 px-6 py-16 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        </span>
                        <p class="font-semibold" style="color: var(--c-heading)">Geen aanvragen gevonden</p>
                        <p class="text-xs" style="color: var(--c-muted)">Pas je zoekopdracht of filters aan.</p>
                    </div>
                </div>

                {{-- Pagination (compact) --}}
                <div id="inboxPagination" class="inbox-pagination shrink-0 border-t px-3 py-2" style="border-color: rgba(148, 163, 184, 0.15)"></div>
            </div>

            {{-- ============ RIGHT: chat ============ --}}
            <div id="inboxChatPane"
                 class="inbox-pane hidden min-h-0 flex-col overflow-hidden rounded-2xl border lg:flex"
                 style="background-color: var(--c-card); border-color: rgba(148, 163, 184, 0.2); box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06)">

                {{-- Empty state --}}
                <div id="inboxChatEmpty" class="flex h-full flex-col items-center justify-center gap-3 px-6 py-10 text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>
                    </span>
                    <p class="font-semibold" style="color: var(--c-heading)">Selecteer een bericht</p>
                    <p class="max-w-xs text-xs" style="color: var(--c-muted)">Klik links op een aanvraag om het gesprek te openen en te beantwoorden.</p>
                </div>

                {{-- Chat --}}
                <div id="inboxChat" class="hidden min-h-0 flex-1 flex-col">

                    {{-- Chat header --}}
                    <div class="shrink-0 border-b px-4 py-3.5 sm:px-5" style="border-color: rgba(148, 163, 184, 0.15)">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2.5">
                            <div class="flex min-w-0 items-center gap-3">
                                <button type="button" id="inboxBackBtn"
                                        class="rounded-lg p-2 transition hover:bg-blue-50 hover:text-blue-600 lg:hidden dark:hover:bg-blue-900/30" style="color: var(--c-muted)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                    </svg>
                                </button>
                                <span id="inboxAvatar"
                                      class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)]">?</span>
                                <div class="min-w-0">
                                    <p id="inboxName" class="truncate font-extrabold" style="color: var(--c-heading)">—</p>
                                    <p id="inboxMeta" class="truncate text-xs" style="color: var(--c-muted)">—</p>
                                </div>
                            </div>

                            <div class="flex shrink-0 items-center gap-2">
                                <a id="inboxAttachmentBtn" href="#" target="_blank"
                                   class="hidden items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition hover:border-blue-400 hover:text-blue-600"
                                   style="color: var(--c-heading); border-color: var(--c-input-border)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" />
                                    </svg>
                                    Bestand
                                </a>
                                <select id="inboxStatusSelect"
                                        class="h-9 cursor-pointer appearance-none rounded-lg border py-1.5 pl-3 pr-8 text-xs font-bold leading-none outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                                        style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 12px;">
                                    <option value="new">Nieuw</option>
                                    <option value="in_progress">In behandeling</option>
                                    <option value="replied">Beantwoord</option>
                                    <option value="closed">Gesloten</option>
                                </select>
                                <button type="button" id="inboxDeleteBtn" title="Verwijder"
                                        class="rounded-lg p-2 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 dark:hover:text-red-400" style="color: var(--c-muted)">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Compact meta chips --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px]">
                            <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148, 163, 184, 0.15); color: var(--c-heading)">
                                Onderwerp: <span id="inboxSubject" class="font-bold">—</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148, 163, 184, 0.15); color: var(--c-heading)">
                                Type: <span id="inboxType" class="font-bold">—</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148, 163, 184, 0.15); color: var(--c-heading)">
                                Tel: <span id="inboxPhone" class="font-bold">—</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 rounded-md border px-2 py-1 font-semibold" style="border-color: rgba(148, 163, 184, 0.15); color: var(--c-heading)">
                                Ontvangen: <span id="inboxDate" class="font-bold">—</span>
                            </span>
                        </div>
                    </div>

                    {{-- Thread (scrolls) --}}
                    <div id="inboxThread" class="inbox-scroll min-h-0 flex-1 space-y-4 overflow-y-auto px-4 py-5 sm:px-6"></div>

                    {{-- Reply box --}}
                    <div class="shrink-0 border-t px-4 py-4 sm:px-5" style="border-color: rgba(148, 163, 184, 0.15)">
                        <div class="flex items-end gap-3">
                            <textarea id="inboxReply" rows="2" placeholder="Typ je antwoord hier..."
                                      class="form-input flex-1 resize-none text-sm" style="min-height: 52px"></textarea>
                            <button type="button" id="inboxReplyBtn"
                                    class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-5 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                </svg>
                                Versturen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ Delete confirm modal ============ --}}
    <x-admin.modal id="inboxDeleteModal" title="Aanvraag verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </span>

            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je
                    <span id="inboxDeleteName" class="font-bold">deze aanvraag</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">
                    Het volledige gesprek en eventuele bijlagen worden permanent verwijderd.
                </p>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>

            <button type="button" id="inboxDeleteConfirmBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] transition duration-300 hover:-translate-y-0.5 hover:bg-red-700">
                Ja, verwijderen
            </button>
        </x-slot>
    </x-admin.modal>
</x-admin.layout>