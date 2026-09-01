<x-admin.layout title="Categorieën">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Categorieën</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Beheer alle productcategorieën voor de webshop.</p>
            </div>
            <button type="button" id="openCreateCategory"
                    class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuwe categorie
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
                        <input type="text" id="categorySearch" placeholder="Zoek op naam of slug..."
                               class="form-input h-10 w-full pl-12 text-sm" style="background-color: var(--c-page)">
                    </div>
                    <select id="categoryStatusFilter"
                            class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="all">Alle statussen</option>
                        <option value="1">Actief</option>
                        <option value="0">Inactief</option>
                    </select>
                    <select id="categoryPerPage"
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
                <table class="w-full border-collapse text-left">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="w-[100px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Afbeelding</th>
                            <th class="min-w-[160px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Naam</th>
                            <th class="min-w-[120px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Slug</th>
                            <th class="w-[90px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap text-center" style="color: var(--c-muted)">Producten</th>
                            <th class="w-[120px] px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[160px] min-w-[160px] max-w-[160px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTableBody"></tbody>
                </table>
            </div>
            <div id="categoryPagination" class="shrink-0 border-t px-3 py-2" style="border-color: rgba(148,163,184,.15)"></div>
        </div>
    </div>

    {{-- Create / Edit modal --}}
    <x-admin.modal id="categoryFormModal" title="Nieuwe categorie" subtitle="Voeg een nieuwe categorie toe" size="lg">
        <form id="categoryForm" novalidate>
            @csrf
            <input type="hidden" name="category_id" id="categoryId">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <x-input-label for="c-name">Naam <span class="text-red-500">*</span></x-input-label>
                    <x-text-input id="c-name" name="name" placeholder="Bijv. Laptops" />
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="c-status">Status <span class="text-red-500">*</span></x-input-label>
                    <select id="c-status" name="status"
                            class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                            style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        <option value="1">Actief</option>
                        <option value="0">Inactief</option>
                    </select>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                </div>

                <div>
                    <x-input-label for="c-image">Afbeelding</x-input-label>
                    <input type="file" id="c-image" name="image" accept=".jpg,.jpeg,.png,.webp"
                           class="block w-full cursor-pointer rounded-xl border text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100"
                           style="border-color: var(--c-input-border); color: var(--c-muted)">
                    <p class="mt-1 text-xs" style="color: var(--c-muted)">JPG, PNG, WEBP — max 10MB</p>
                    <p class="field-error mt-1 hidden text-xs font-medium text-red-500"></p>
                    <div id="c-image-preview" class="mt-3 hidden">
                        <img src="" alt="" class="h-20 w-28 rounded-xl border object-cover" style="border-color: rgba(148,163,184,.2)">
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
            <button type="button" id="categorySaveBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                <span data-btn-label>Categorie opslaan</span>
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- Details modal --}}
    <x-admin.modal id="categoryDetailsModal" title="Categorie details" size="lg">
        <div id="categoryDetailsContent" class="space-y-5"></div>
        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Sluiten
            </button>
            <button type="button" id="categoryDetailsDeleteBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-red-200 px-5 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-900/50 dark:text-red-400 dark:hover:bg-red-950/40">
                Verwijderen
            </button>
            <button type="button" id="categoryDetailsEditBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5">
                Bewerken
            </button>
        </x-slot>
    </x-admin.modal>

    {{-- Delete confirm --}}
    <x-admin.modal id="categoryDeleteModal" title="Categorie verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je <span id="deleteCategoryName" class="font-bold">deze categorie</span> wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Categorieën met producten kunnen niet worden verwijderd. Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
            <button type="button" id="categoryDeleteConfirmBtn"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] transition duration-300 hover:-translate-y-0.5 hover:bg-red-700">
                Ja, verwijderen
            </button>
        </x-slot>
    </x-admin.modal>

    @push('scripts')
    <script src="{{ asset('assets/js/admin/categories.js') }}?v={{ filemtime(public_path('assets/js/admin/categories.js')) }}"></script>
    @endpush
</x-admin.layout>
