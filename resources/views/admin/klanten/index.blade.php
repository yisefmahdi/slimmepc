<x-admin.layout title="Klanten">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Klantenbeheer</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer alle klanten, techniciens en beheerders van Slimme-PC.</p>
            </div>
            <button type="button" data-open-create
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuwe klant
            </button>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </span>
                        <input type="text" id="klantSearch" placeholder="Zoek op naam, e-mail, klantnummer, stad..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="klantRoleFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-32"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="">Alle rollen</option>
                        <option value="user">Klanten</option>
                        <option value="technician">Techniciens</option>
                        <option value="admin">Beheerders</option>
                    </select>
                    <select id="klantPerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-[110px]"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="10">10 per pagina</option>
                        <option value="25">25 per pagina</option>
                        <option value="50">50 per pagina</option>
                    </select>
                </div>
                {{-- Stats chips --}}
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="countUsers">Klanten: 0</span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1.5 font-bold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400" id="countTechnicians">Techniciens: 0</span>
                    <span class="rounded-full bg-purple-50 px-3 py-1.5 font-bold text-purple-600 dark:bg-purple-900/30 dark:text-purple-400" id="countAdmins">Beheerders: 0</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto overflow-x-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full min-w-[1100px] border-collapse text-left" style="min-width:1100px">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Klant</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Klantnummer</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Adres</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Rol</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="klantTableBody"></tbody>
                </table>
            </div>
            <div id="klantPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- ============ Create / Edit modal ============ --}}
    <x-admin.modal id="klantFormModal" title="Nieuwe klant" subtitle="Voeg een nieuwe klant toe aan Slimme-PC" size="lg">
        <form id="klantForm" novalidate>
            @csrf

            <input type="hidden" name="klant_id" id="klantId">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="k-name">Volledige naam <span class="text-red-500">*</span></x-input-label>
                    <x-text-input id="k-name" name="name" placeholder="Vul de volledige naam in" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-email">E-mailadres <span class="text-red-500">*</span></x-input-label>
                    <x-text-input id="k-email" name="email" type="email" placeholder="naam@voorbeeld.nl" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-phone">Telefoonnummer</x-input-label>
                    <x-text-input id="k-phone" name="phone" placeholder="06 12345678" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-klantnummer">Klantnummer</x-input-label>
                    <x-text-input id="k-klantnummer" name="klantnummer" placeholder="Automatisch gegenereerd" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-city">Stad</x-input-label>
                    <x-text-input id="k-city" name="city" placeholder="Bijv. Utrecht" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-street">Straat</x-input-label>
                    <x-text-input id="k-street" name="street" placeholder="Straatnaam" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-house">Huisnummer</x-input-label>
                    <x-text-input id="k-house" name="house_number" placeholder="123" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-postcode">Postcode</x-input-label>
                    <x-text-input id="k-postcode" name="postcode" placeholder="1234 AB" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-role">Rol</x-input-label>
                    <select id="k-role" name="role"
                            class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="user">Klant</option>
                        <option value="technician">Technicien</option>
                        <option value="admin">Beheerder</option>
                    </select>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="k-password">
                        Wachtwoord <span id="passwordOptionalHint" class="text-xs font-normal" style="color: var(--c-muted)">(optioneel — automatisch gegenereerd)</span>
                    </x-input-label>
                    <x-text-input id="k-password" name="password" type="text" placeholder="Minimaal 8 tekens" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>

            <button type="button" id="klantSaveBtn" data-save-klant
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                <span data-btn-label>Klant opslaan</span>
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- ============ Details modal ============ --}}
    <x-admin.modal id="klantDetailsModal" title="Klantgegevens" size="lg">
        <div id="klantDetailsContent" class="space-y-5">
            {{-- filled by JS --}}
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Sluiten
            </button>

            <button type="button" id="klantDetailsDeleteBtn" data-delete-from-details
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-red-200 px-5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-950/40">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                Verwijderen
            </button>

            <button type="button" id="klantDetailsEditBtn" data-edit-from-details
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Bewerken
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- ============ Delete confirm modal ============ --}}
    <x-admin.modal id="klantDeleteModal" title="Klant verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </span>

            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je
                    <span id="deleteKlantName" class="font-bold">deze klant</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">
                    Deze actie kan niet ongedaan worden gemaakt. Alle gegevens van deze klant worden permanent verwijderd.
                </p>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>

            <button type="button" id="klantDeleteConfirmBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] transition duration-300 hover:-translate-y-0.5 hover:bg-red-700">
                Ja, verwijderen
            </button>
        </x-slot>
    </x-admin.modal>
</x-admin.layout>

