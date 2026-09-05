<x-admin.layout title="Kortingscodes">
    <style>
        .apple-switch{position:relative;display:inline-flex;height:24px;width:44px;flex-shrink:0;cursor:pointer;border-radius:9999px;border:2px solid transparent;transition:background-color .2s ease-in-out;outline:none;padding:0;vertical-align:middle}
        .apple-switch.is-active{background-color:#10b981}
        .apple-switch.is-inactive{background-color:#cbd5e1}
        .dark .apple-switch.is-inactive{background-color:#475569}
        .apple-switch .apple-knob{pointer-events:none;display:inline-block;height:20px;width:20px;border-radius:9999px;background-color:#fff;box-shadow:0 2px 5px rgba(0,0,0,.25);transition:transform .2s cubic-bezier(.4,0,0.2,1);transform:translateX(0)}
        .apple-switch.is-active .apple-knob{transform:translateX(20px)}
    </style>
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Kortingscodes</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer kortingscodes voor de winkelwagen.</p>
            </div>
            <button type="button" id="openCreateCoupon"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuwe code
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
                        <input type="text" id="couponSearch" placeholder="Zoek op code of naam..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="couponStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="all">Alle statussen</option>
                        <option value="1">Actief</option>
                        <option value="0">Inactief</option>
                    </select>
                    <select id="couponPerPage"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-[110px]"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="10">10 per pagina</option>
                        <option value="25">25 per pagina</option>
                        <option value="50">50 per pagina</option>
                    </select>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="countTotal">Totaal: 0</span>
                    <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400" id="countActive">Actief: 0</span>
                    <span class="rounded-full bg-red-50 px-3 py-1.5 font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400" id="countInactive">Inactief: 0</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width: 860px;">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Code</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)">Type</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)">Waarde</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)">Min. bedrag</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)">Gebruik</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Geldigheid</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[140px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="couponTableBody"></tbody>
                </table>
            </div>
            <div id="couponPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Create / Edit modal --}}
    <x-admin.modal id="couponFormModal" title="Nieuwe kortingscode" subtitle="Voeg een kortingscode toe" size="lg">
        <form id="couponForm" novalidate>
            @csrf
            <input type="hidden" name="coupon_id" id="couponId">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="cp-code">Code <span class="text-red-500">*</span></x-input-label>
                    <div class="flex gap-2">
                        <x-text-input id="cp-code" name="code" placeholder="Bijv. ZOMER20" class="uppercase flex-1" />
                        <button type="button" id="generateCodeBtn" class="shrink-0 rounded-xl border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-600 hover:bg-blue-100">Genereer</button>
                    </div>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-name">Naam</x-input-label>
                    <x-text-input id="cp-name" name="name" placeholder="Bijv. Zomeractie 2026" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-discount-type">Type <span class="text-red-500">*</span></x-input-label>
                    <select id="cp-discount-type" name="discount_type"
                            class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Vast bedrag (€)</option>
                    </select>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-discount-value">Waarde <span class="text-red-500">*</span></x-input-label>
                    <x-text-input id="cp-discount-value" name="discount_value" type="number" step="0.01" min="0.01" placeholder="Bijv. 20" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-min-amount">Min. bestelbedrag (€)</x-input-label>
                    <x-text-input id="cp-min-amount" name="min_amount" type="number" step="0.01" min="0" placeholder="Geen minimum" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-usage-limit">Limiet (aantal keer)</x-input-label>
                    <x-text-input id="cp-usage-limit" name="usage_limit" type="number" min="1" placeholder="Onbeperkt" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-start-date">Startdatum</x-input-label>
                    <x-text-input id="cp-start-date" name="start_date" type="datetime-local" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-end-date">Einddatum</x-input-label>
                    <x-text-input id="cp-end-date" name="end_date" type="datetime-local" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div>
                    <x-input-label for="cp-status">Status <span class="text-red-500">*</span></x-input-label>
                    <select id="cp-status" name="status"
                            class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="1">Actief</option>
                        <option value="0">Inactief</option>
                    </select>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>
                <div class="flex items-end pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="cp-single-use" name="is_single_use" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium" style="color: var(--c-heading)">Eenmalig per klant</span>
                    </label>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
            <button type="button" id="couponSaveBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                <span data-btn-label>Opslaan</span>
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete confirm --}}
    <x-admin.modal id="couponDeleteModal" title="Kortingscode verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="deleteCouponCode" class="font-bold">deze code</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
            <button type="button" id="couponDeleteConfirmBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] transition duration-300 hover:-translate-y-0.5 hover:bg-red-700">
                Ja, verwijderen
            </button>
        </x-slot>
    </x-admin.modal>

</x-admin.layout>
