<x-admin.layout title="Producten">
    <style>
        .apple-switch {
            position: relative;
            display: inline-flex;
            height: 24px;
            width: 44px;
            flex-shrink: 0;
            cursor: pointer;
            border-radius: 9999px;
            border: 2px solid transparent;
            transition: background-color 0.2s ease-in-out;
            outline: none;
            padding: 0;
            vertical-align: middle;
        }
        .apple-switch.is-active {
            background-color: #10b981;
        }
        .apple-switch.is-inactive {
            background-color: #cbd5e1;
        }
        .dark .apple-switch.is-inactive {
            background-color: #475569;
        }
        .apple-switch .apple-knob {
            pointer-events: none;
            display: inline-block;
            height: 20px;
            width: 20px;
            border-radius: 9999px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(0px);
        }
        .apple-switch.is-active .apple-knob {
            transform: translateX(20px);
        }
    </style>
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Producten</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer alle producten voor de webshop.</p>
            </div>
            <a href="{{ route('admin.webshop.products.create') }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuw product
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <div class="relative flex-1">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </span>
                            <input type="text" id="productSearch" placeholder="Zoek op titel, merk of slug..."
                                   class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                        </div>
                        <select id="productCategoryFilter"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-44"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="all">Alle categorieën</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <select id="productStatusFilter"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-32"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="all">Alle statussen</option>
                            <option value="1">Actief</option>
                            <option value="0">Inactief</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <select id="productBrandFilter"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-44"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="all">Alle merken</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}">{{ $brand }}</option>
                            @endforeach
                        </select>
                        <select id="productStockFilter"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-36"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="all">Alle voorraad</option>
                            <option value="in_stock">Op voorraad</option>
                            <option value="out_of_stock">Niet op voorraad</option>
                        </select>
                        <div class="flex gap-2">
                            <input type="number" id="productMinPrice" placeholder="Min €" step="0.01"
                                   class="h-9 w-24 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <input type="number" id="productMaxPrice" placeholder="Max €" step="0.01"
                                   class="h-9 w-24 rounded-lg border px-2 text-xs outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        </div>
                        <select id="productPerPage"
                                class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-[110px] sm:ml-auto"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="15">15 per pagina</option>
                            <option value="10">10 per pagina</option>
                            <option value="25">25 per pagina</option>
                            <option value="50">50 per pagina</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400" id="countTotal">Totaal: 0</span>
                    <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400" id="countActive">Actief: 0</span>
                    <span class="rounded-full bg-red-50 px-3 py-1.5 font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400" id="countInactive">Inactief: 0</span>
                    <span class="rounded-full bg-amber-50 px-3 py-1.5 font-bold text-amber-600 dark:bg-amber-900/30 dark:text-amber-400" id="countInStock">Op voorraad: 0</span>
                    <span class="rounded-full bg-indigo-50 px-3 py-1.5 font-bold text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400" id="countFeatured">Op Home: 0</span>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="w-[70px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Afbeelding</th>
                            <th class="min-w-[180px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam</th>
                            <th class="w-[90px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Merk</th>
                            <th class="w-[110px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Categorie</th>
                            <th class="w-[90px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Prijs</th>
                            <th class="w-[90px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Oude prijs</th>
                            <th class="w-[70px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Korting</th>
                            <th class="w-[80px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Voorraad</th>
                            <th class="w-[85px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Home</th>
                            <th class="w-[110px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)"><span class="inline-flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 text-amber-400"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.37 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.84-.197-1.54-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.34 8.719c-.783-.57-.38-1.81.588-1.81h3.462a1 1 0 00.95-.69l1.07-3.292z"/></svg>Beoordeling</span></th>
                            <th class="w-[85px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[110px] min-w-[110px] max-w-[110px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody"></tbody>
                </table>
            </div>
            <div id="productPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Details --}}
    <x-admin.modal id="productDetailsModal" title="Product details" size="xl">
        <div id="productDetailsContent" class="space-y-5"></div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Sluiten</button>
            <button type="button" id="productDetailsDeleteBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-red-200 px-5 text-sm font-semibold text-red-600 hover:bg-red-50">Verwijderen</button>
            <button type="button" id="productDetailsEditBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white">Bewerken</button>
        </x-slot>
    </x-admin.modal>

    {{-- Reviews --}}
    <x-admin.modal id="productReviewsModal" title="Beoordelingen" size="lg">
        <div id="productReviewsHeader" class="mb-4 flex items-center gap-3 text-sm" style="color: var(--c-muted)"></div>
        <div id="productReviewsStats" class="mb-4 grid grid-cols-3 gap-2 text-center text-xs"></div>
        <div id="productReviewsList" class="space-y-3 max-h-[50vh] overflow-auto pr-1"></div>
        <div id="productReviewsEmpty" class="hidden py-8 text-center text-sm" style="color: var(--c-muted)">Geen beoordelingen gevonden.</div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Sluiten</button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete --}}
    <x-admin.modal id="productDeleteModal" title="Product verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="deleteProductName" class="font-bold">dit product</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <button type="button" id="productDeleteConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script>
        window.shopCategories = @json($categories);
    </script>
    <script src="{{ asset('assets/js/admin/products.js') }}?v={{ filemtime(public_path('assets/js/admin/products.js')) }}"></script>
    @endpush
</x-admin.layout>
