<x-admin.layout title="Product bewerken">


    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">Product bewerken</h1>
            <p class="text-xs" style="color: var(--c-muted)">{{ $product->title }}</p>
        </div>
        <x-admin.back-button :href="route('admin.webshop.products.index')" />
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc ps-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.webshop.products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Algemene gegevens</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="title">Titel <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="title" name="title" value="{{ old('title', $product->title) }}" placeholder="Bijv. HP Pavilion 15" />
                        @error('title')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="category_id">Categorie <span class="text-red-500">*</span></x-input-label>
                        <select id="category_id" name="category_id" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="brand">Merk</x-input-label>
                        <x-text-input id="brand" name="brand" value="{{ old('brand', $product->brand) }}" placeholder="Bijv. HP" />
                        @error('brand')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="sku">SKU</x-input-label>
                        <x-text-input id="sku" name="sku" value="{{ old('sku', $product->sku) }}" placeholder="Automatisch of eigen code" />
                        @error('sku')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="price">Prijs (€) <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="price" name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" placeholder="0.00" />
                        @error('price')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="old_price">Oude prijs (€)</x-input-label>
                        <x-text-input id="old_price" name="old_price" type="number" step="0.01" value="{{ old('old_price', $product->old_price) }}" placeholder="0.00" />
                        @error('old_price')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="delivery_time">Levertijd</x-input-label>
                        <x-text-input id="delivery_time" name="delivery_time" value="{{ old('delivery_time', $product->delivery_time) }}" placeholder="Bijv. 1-2 werkdagen" />
                        @error('delivery_time')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Voorraad & Status</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="stock_status">Voorraad status <span class="text-red-500">*</span></x-input-label>
                        <select id="stock_status" name="stock_status" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="in_stock" @selected(old('stock_status', $product->stock_status)=='in_stock')>Op voorraad</option>
                            <option value="out_of_stock" @selected(old('stock_status', $product->stock_status)=='out_of_stock')>Niet op voorraad</option>
                        </select>
                        @error('stock_status')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="status">Status <span class="text-red-500">*</span></x-input-label>
                        <select id="status" name="status" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="1" @selected(old('status', $product->status)=='1')>Actief</option>
                            <option value="0" @selected(old('status', $product->status)=='0')>Inactief</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="is_featured">Tonen op Home (Populair)</x-input-label>
                        <select id="is_featured" name="is_featured" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="0" @selected(old('is_featured', $product->is_featured ? '1' : '0')=='0')>Nee (Niet tonen)</option>
                            <option value="1" @selected(old('is_featured', $product->is_featured ? '1' : '0')=='1')>Ja (Toon op homepage)</option>
                        </select>
                        @error('is_featured')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Korting</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="discount_type">Korting type</x-input-label>
                        <select id="discount_type" name="discount_type" class="h-[52px] w-full rounded-xl border px-4 text-[15px] outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                            <option value="">Geen korting</option>
                            <option value="percentage" @selected(old('discount_type', $product->discount_type)=='percentage')>Percentage (%)</option>
                            <option value="fixed" @selected(old('discount_type', $product->discount_type)=='fixed')>Vast bedrag (€)</option>
                        </select>
                        @error('discount_type')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="discount_value">Korting waarde</x-input-label>
                        <x-text-input id="discount_value" name="discount_value" type="number" step="0.01" value="{{ old('discount_value', $product->discount_value) }}" placeholder="0.00" />
                        @error('discount_value')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="discount_start_date">Korting startdatum</x-input-label>
                        <x-text-input id="discount_start_date" name="discount_start_date" type="date" value="{{ old('discount_start_date', $product->discount_start_date ? $product->discount_start_date->format('Y-m-d') : '') }}" />
                        @error('discount_start_date')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="discount_end_date">Korting einddatum</x-input-label>
                        <x-text-input id="discount_end_date" name="discount_end_date" type="date" value="{{ old('discount_end_date', $product->discount_end_date ? $product->discount_end_date->format('Y-m-d') : '') }}" />
                        @error('discount_end_date')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Beschrijving & Media</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                <div>
                    <div class="mb-2.5 flex items-center justify-between">
                        <x-input-label for="description" class="!mb-0">Beschrijving</x-input-label>
                        <button type="button" id="btnAiGenerate" 
                                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold transition hover:opacity-90 active:scale-95 focus:outline-none"
                                style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 50%, #2563eb 100%); color: #ffffff; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4" style="color: #ffffff;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                            </svg>
                            <span style="color: #ffffff; font-weight: 700; font-size: 12px; letter-spacing: 0.02em;">Genereer met AI</span>
                        </button>
                    </div>
                    <div id="ai-status-indicator" class="hidden mb-3 rounded-xl p-3 text-xs" style="background-color: rgba(124, 58, 237, 0.08); border: 1px solid rgba(124, 58, 237, 0.25); color: #6d28d9;">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 animate-spin shrink-0" style="color: #7c3aed;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="ai-status-text" class="font-semibold">Zoeken naar productspecificaties op het internet...</span>
                        </div>
                    </div>
                    <div class="rounded-xl overflow-hidden border" style="border-color: var(--c-input-border, rgba(148,163,184,.3));">
                        <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
                    </div>
                    @error('description')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-6">
                    {{-- Main Image Field --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <x-input-label for="main_image" class="!mb-0">Hoofdafbeelding</x-input-label>
                            <span id="main-image-badge" class="{{ $product->main_image ? '' : 'hidden' }} rounded-full bg-blue-50 px-2.5 py-0.5 text-[11px] font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $product->main_image ? 'Huidige afbeelding' : 'Geselecteerd' }}
                            </span>
                        </div>

                        <input type="file" id="main_image" name="main_image" accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">
                        <input type="hidden" id="remove_main_image" name="remove_main_image" value="0">

                        {{-- Dropzone when empty --}}
                        <div id="main-image-dropzone"
                             class="{{ $product->main_image ? 'hidden ' : '' }}group relative flex cursor-pointer flex-col items-center justify-center gap-2.5 rounded-2xl border-2 border-dashed border-blue-200/80 bg-blue-50/30 p-6 text-center transition hover:border-blue-500 hover:bg-blue-50/70 dark:border-blue-900/40 dark:bg-slate-900/20 dark:hover:border-blue-500">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-blue-600 shadow-sm transition group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white dark:bg-slate-800 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                    Sleep hoofdafbeelding hierheen of <span class="text-blue-600 hover:underline">klik om te kiezen</span>
                                </p>
                                <p class="mt-1 text-xs text-slate-400">PNG, JPG, WEBP of AVIF (max. 10MB)</p>
                            </div>
                        </div>

                        {{-- Preview Card when file is chosen or existing --}}
                        <div id="main-image-card" class="{{ $product->main_image ? '' : 'hidden ' }}rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4 min-w-0 flex-1">
                                    <div class="relative h-20 w-20 shrink-0 overflow-hidden rounded-xl border border-slate-200/80 bg-slate-50 dark:border-slate-800 dark:bg-slate-800 shadow-inner flex items-center justify-center">
                                        <img id="main-image-preview-img" src="{{ $product->main_image ? asset('storage/' . $product->main_image) : '' }}" alt="Hoofdafbeelding" class="h-full w-full object-cover">
                                    </div>
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Hoofdafbeelding</span>
                                            <span id="main-image-size" class="text-xs font-medium text-slate-400">{{ $product->main_image ? 'Opgeslagen' : '' }}</span>
                                            <span id="main-image-status-note" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                                <svg class="h-3.5 w-3.5 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/></svg>
                                                {{ $product->main_image ? 'Actieve hoofdafbeelding' : 'Klaar voor opslaan' }}
                                            </span>
                                        </div>
                                        <p id="main-image-name" class="text-sm font-bold text-slate-800 dark:text-slate-100 truncate">
                                            {{ $product->main_image ? basename($product->main_image) : '' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                    <button type="button" id="main-image-replace-btn" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 text-xs font-bold text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700 transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                                        Vervangen
                                    </button>
                                    <button type="button" id="main-image-remove-btn" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3.5 text-xs font-bold text-red-600 hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-400 transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                        Verwijderen
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('main_image')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>

                    {{-- Gallery Field --}}
                    @php $existingGallery = (array) ($product->gallery_images ?? []); @endphp
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <x-input-label for="gallery_images" class="!mb-0">Galerij afbeeldingen</x-input-label>
                                <span id="gallery-counter" class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                    {{ count($existingGallery) }} / 10
                                </span>
                            </div>
                            <button type="button" id="gallery-clear-new-btn" class="hidden text-xs font-bold text-red-500 hover:text-red-700 hover:underline">
                                Nieuwe selectie wissen
                            </button>
                        </div>

                        <input type="file" id="gallery_images" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">

                        {{-- Dropzone (When 0 total images) --}}
                        <div id="gallery-dropzone"
                             class="{{ count($existingGallery) > 0 ? 'hidden ' : '' }}group relative flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-blue-200/80 bg-blue-50/30 p-5 text-center transition hover:border-blue-500 hover:bg-blue-50/70 dark:border-blue-900/40 dark:bg-slate-900/20 dark:hover:border-blue-500">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm transition group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white dark:bg-slate-800 dark:text-blue-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                    Sleep extra afbeeldingen hierheen of <span class="text-blue-600 hover:underline">klik om toe te voegen</span>
                                </p>
                                <p class="mt-0.5 text-xs text-slate-400">Meerdere bestanden tegelijk mogelijk · Maximaal 10 afbeeldingen totaal (PNG, JPG, WEBP, AVIF)</p>
                            </div>
                        </div>

                        {{-- Compact Preview Tiles (Existing + Staged New) --}}
                        <div id="gallery-grid" class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                            @foreach($existingGallery as $index => $img)
                                <div class="existing-gallery-item group relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900" data-path="{{ $img }}">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Galerij afbeelding" class="h-full w-full object-cover">
                                    <input type="hidden" name="existing_gallery[]" value="{{ $img }}" class="existing-gallery-input">
                                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-0 transition-opacity group-hover:opacity-100"></div>
                                    <span style="background-color: rgba(15, 23, 42, 0.85);" class="absolute left-1.5 top-1.5 rounded px-1.5 py-0.5 text-[9px] font-bold text-white shadow-sm backdrop-blur">
                                        {{ $index + 1 }}
                                    </span>
                                    <button type="button" style="background-color: rgba(239, 68, 68, 0.95);" class="btn-remove-existing absolute right-1.5 top-1.5 flex h-5 w-5 items-center justify-center rounded-full text-white shadow hover:bg-red-600 transition" title="Verwijderen uit galerij">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            @endforeach

                            @if(count($existingGallery) < 10)
                                <div id="gallery-add-tile" class="flex h-20 w-20 sm:h-24 sm:w-24 shrink-0 cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/40 text-blue-600 transition hover:border-blue-500 hover:bg-blue-50/80 shadow-sm" title="Afbeelding toevoegen">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                    <span class="text-[10px] font-bold">Toevoegen</span>
                                </div>
                            @endif
                        </div>

                        {{-- Dynamic client feedback message --}}
                        <p id="gallery-feedback" class="hidden text-xs font-semibold text-amber-600 dark:text-amber-400"></p>

                        @error('gallery_images')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                        @error('gallery_images.*')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <x-input-label>Features</x-input-label>
                    <div id="features-container" class="space-y-2">
                        @php $features = old('features', $product->features ?? ['']); if(empty(array_filter((array)$features))) $features=['']; @endphp
                        @foreach((array)$features as $f)
                            <div class="flex gap-2"><input type="text" name="features[]" value="{{ $f }}" placeholder="Feature" class="form-input h-10 flex-1 text-sm" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)"><button type="button" onclick="this.parentElement.remove()" class="rounded-lg border p-2 text-red-500 hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                        @endforeach
                    </div>
                    <button type="button" onclick="addField('features-container','features')" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-100">+ Feature toevoegen</button>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label>Kleuren</x-input-label>
                        <div id="colors-container" class="space-y-2">
                            @php $colors = old('colors', $product->colors ?? ['']); if(empty(array_filter((array)$colors))) $colors=['']; @endphp
                            @foreach((array)$colors as $c)
                                <div class="flex gap-2"><input type="text" name="colors[]" value="{{ $c }}" placeholder="Bijv. Zwart" class="form-input h-10 flex-1 text-sm" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)"><button type="button" onclick="this.parentElement.remove()" class="rounded-lg border p-2 text-red-500 hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addField('colors-container','colors')" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-100">+ Kleur toevoegen</button>
                    </div>
                    <div>
                        <x-input-label>Maten</x-input-label>
                        <div id="sizes-container" class="space-y-2">
                            @php $sizes = old('sizes', $product->sizes ?? ['']); if(empty(array_filter((array)$sizes))) $sizes=['']; @endphp
                            @foreach((array)$sizes as $s)
                                <div class="flex gap-2"><input type="text" name="sizes[]" value="{{ $s }}" placeholder="Bijv. M" class="form-input h-10 flex-1 text-sm" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)"><button type="button" onclick="this.parentElement.remove()" class="rounded-lg border p-2 text-red-500 hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addField('sizes-container','sizes')" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-100">+ Maat toevoegen</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="external_link">Externe link</x-input-label>
                        <x-text-input id="external_link" name="external_link" value="{{ old('external_link', $product->external_link) }}" placeholder="https://..." />
                        @error('external_link')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="manual_url">Handleiding URL</x-input-label>
                        <x-text-input id="manual_url" name="manual_url" value="{{ old('manual_url', $product->manual_url) }}" placeholder="https://..." />
                        @error('manual_url')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="download_32bit_url">Download 32-bit URL</x-input-label>
                        <x-text-input id="download_32bit_url" name="download_32bit_url" value="{{ old('download_32bit_url', $product->download_32bit_url) }}" placeholder="https://..." />
                        @error('download_32bit_url')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="download_64bit_url">Download 64-bit URL</x-input-label>
                        <x-text-input id="download_64bit_url" name="download_64bit_url" value="{{ old('download_64bit_url', $product->download_64bit_url) }}" placeholder="https://..." />
                        @error('download_64bit_url')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.webshop.products.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border px-6 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</a>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-8 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] hover:-translate-y-0.5">Wijzigingen opslaan</button>
        </div>
    </form>

    <script>
        function addField(containerId, name) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `<input type="text" name="${name}[]" placeholder="Waarde" class="form-input h-10 flex-1 text-sm" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)"><button type="button" onclick="this.parentElement.remove()" class="rounded-lg border p-2 text-red-500 hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
            container.appendChild(div);
        }

        // -------------------------------------------------------------
        // Helpers
        // -------------------------------------------------------------
        function formatBytes(bytes) {
            if (!bytes || bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        // -------------------------------------------------------------
        // Main Image Handling
        // -------------------------------------------------------------
        const mainInput = document.getElementById('main_image');
        const removeMainImageInput = document.getElementById('remove_main_image');
        const mainDropzone = document.getElementById('main-image-dropzone');
        const mainCard = document.getElementById('main-image-card');
        const mainPreviewImg = document.getElementById('main-image-preview-img');
        const mainName = document.getElementById('main-image-name');
        const mainSize = document.getElementById('main-image-size');
        const mainStatusNote = document.getElementById('main-image-status-note');
        const mainBadge = document.getElementById('main-image-badge');
        const mainReplaceBtn = document.getElementById('main-image-replace-btn');
        const mainRemoveBtn = document.getElementById('main-image-remove-btn');

        function setMainImage(file) {
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                mainPreviewImg.src = e.target.result;
                mainName.textContent = file.name;
                mainSize.textContent = `(${formatBytes(file.size)})`;
                if (mainStatusNote) mainStatusNote.textContent = 'Vervangt huidige afbeelding bij opslaan';
                if (mainBadge) {
                    mainBadge.textContent = 'Nieuwe vervanging';
                    mainBadge.className = 'rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                    mainBadge.classList.remove('hidden');
                }
                if (removeMainImageInput) removeMainImageInput.value = '0';
                mainDropzone.classList.add('hidden');
                mainCard.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removeMainImage() {
            mainInput.value = '';
            mainPreviewImg.src = '';
            mainName.textContent = '';
            mainSize.textContent = '';
            if (removeMainImageInput) removeMainImageInput.value = '1';
            mainCard.classList.add('hidden');
            mainDropzone.classList.remove('hidden');
            if (mainBadge) mainBadge.classList.add('hidden');
        }

        if (mainDropzone) {
            mainDropzone.addEventListener('click', () => mainInput.click());
            ['dragenter', 'dragover'].forEach(ev => {
                mainDropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    mainDropzone.classList.add('border-blue-500', 'bg-blue-100/50');
                });
            });
            ['dragleave', 'drop'].forEach(ev => {
                mainDropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    mainDropzone.classList.remove('border-blue-500', 'bg-blue-100/50');
                });
            });
            mainDropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                    const file = e.dataTransfer.files[0];
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    mainInput.files = dt.files;
                    setMainImage(file);
                }
            });
        }

        if (mainInput) {
            mainInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    setMainImage(this.files[0]);
                }
            });
        }

        if (mainReplaceBtn) mainReplaceBtn.addEventListener('click', () => mainInput.click());
        if (mainRemoveBtn) mainRemoveBtn.addEventListener('click', removeMainImage);

        // -------------------------------------------------------------
        // Gallery Handling (Existing + Accumulative Staged New)
        // -------------------------------------------------------------
        const galleryInput = document.getElementById('gallery_images');
        const galleryDropzone = document.getElementById('gallery-dropzone');
        const galleryGrid = document.getElementById('gallery-grid');
        const galleryCounter = document.getElementById('gallery-counter');
        const galleryClearNewBtn = document.getElementById('gallery-clear-new-btn');
        const galleryFeedback = document.getElementById('gallery-feedback');

        let stagedGalleryFiles = [];
        const MAX_GALLERY_IMAGES = 10;

        function getExistingCount() {
            return galleryGrid.querySelectorAll('.existing-gallery-item').length;
        }

        function syncGalleryDataTransfer() {
            const dt = new DataTransfer();
            stagedGalleryFiles.forEach(f => dt.items.add(f));
            galleryInput.files = dt.files;
        }

        function updateGalleryAddTile() {
            const totalCount = getExistingCount() + stagedGalleryFiles.length;
            let addTile = document.getElementById('gallery-add-tile');
            if (totalCount < MAX_GALLERY_IMAGES && totalCount > 0) {
                if (!addTile) {
                    addTile = document.createElement('div');
                    addTile.id = 'gallery-add-tile';
                    addTile.className = 'flex h-20 w-20 sm:h-24 sm:w-24 shrink-0 cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/40 text-blue-600 transition hover:border-blue-500 hover:bg-blue-50/80 shadow-sm';
                    addTile.title = 'Afbeelding toevoegen';
                    addTile.innerHTML = `
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        <span class="text-[10px] font-bold">Toevoegen</span>
                    `;
                    addTile.addEventListener('click', () => galleryInput.click());
                }
                galleryGrid.appendChild(addTile);
            } else if (addTile) {
                addTile.remove();
            }
        }

        function updateGalleryCounter() {
            const totalCount = getExistingCount() + stagedGalleryFiles.length;
            if (galleryCounter) {
                galleryCounter.textContent = `${totalCount} / ${MAX_GALLERY_IMAGES}`;
                if (totalCount >= MAX_GALLERY_IMAGES) {
                    galleryCounter.className = 'rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
                } else {
                    galleryCounter.className = 'rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                }
            }

            if (galleryClearNewBtn) {
                if (stagedGalleryFiles.length > 0) galleryClearNewBtn.classList.remove('hidden');
                else galleryClearNewBtn.classList.add('hidden');
            }

            if (galleryDropzone) {
                if (totalCount > 0) {
                    galleryDropzone.classList.add('hidden');
                } else {
                    galleryDropzone.classList.remove('hidden');
                }
            }

            updateGalleryAddTile();
        }

        function attachExistingDeleteListeners() {
            galleryGrid.querySelectorAll('.btn-remove-existing').forEach(btn => {
                btn.onclick = function() {
                    const item = btn.closest('.existing-gallery-item');
                    if (item) {
                        item.remove();
                        updateGalleryCounter();
                    }
                };
            });

            const initialAddTile = document.getElementById('gallery-add-tile');
            if (initialAddTile) {
                initialAddTile.onclick = () => galleryInput.click();
            }
        }
        attachExistingDeleteListeners();

        function renderStagedGallery() {
            galleryGrid.querySelectorAll('.staged-gallery-item').forEach(el => el.remove());

            syncGalleryDataTransfer();

            stagedGalleryFiles.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'staged-gallery-item group relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900';

                const img = document.createElement('img');
                img.className = 'h-full w-full object-cover';
                img.alt = file.name;
                img.src = URL.createObjectURL(file);

                const overlay = document.createElement('div');
                overlay.className = 'pointer-events-none absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/20 opacity-0 transition-opacity group-hover:opacity-100';

                const badge = document.createElement('span');
                badge.style.backgroundColor = 'rgba(16, 185, 129, 0.95)';
                badge.className = 'absolute left-1.5 top-1.5 rounded px-1.5 py-0.5 text-[9px] font-bold text-white shadow-sm backdrop-blur';
                badge.textContent = 'Nieuw';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.style.backgroundColor = 'rgba(239, 68, 68, 0.95)';
                removeBtn.className = 'absolute right-1.5 top-1.5 flex h-5 w-5 items-center justify-center rounded-full text-white shadow hover:bg-red-600 transition';
                removeBtn.title = 'Verwijderen';
                removeBtn.innerHTML = `<svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>`;
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    removeStagedFile(index);
                });

                card.appendChild(img);
                card.appendChild(overlay);
                card.appendChild(badge);
                card.appendChild(removeBtn);

                galleryGrid.appendChild(card);
            });

            updateGalleryCounter();
        }

        function removeStagedFile(index) {
            stagedGalleryFiles.splice(index, 1);
            renderStagedGallery();
        }

        function handleNewGalleryFiles(fileList) {
            const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
            const newFiles = Array.from(fileList).filter(f => validTypes.includes(f.type));

            if (newFiles.length === 0) return;

            const currentTotal = getExistingCount() + stagedGalleryFiles.length;
            const remaining = MAX_GALLERY_IMAGES - currentTotal;

            if (remaining <= 0) {
                showGalleryFeedback(`Je kunt maximaal ${MAX_GALLERY_IMAGES} afbeeldingen in totaal toevoegen.`);
                return;
            }

            if (newFiles.length > remaining) {
                showGalleryFeedback(`Maximaal ${MAX_GALLERY_IMAGES} afbeeldingen toegestaan. Er zijn ${remaining} van de ${newFiles.length} gekozen afbeeldingen toegevoegd.`);
                stagedGalleryFiles.push(...newFiles.slice(0, remaining));
            } else {
                hideGalleryFeedback();
                stagedGalleryFiles.push(...newFiles);
            }

            renderStagedGallery();
        }

        function showGalleryFeedback(msg) {
            if (!galleryFeedback) return;
            galleryFeedback.textContent = msg;
            galleryFeedback.classList.remove('hidden');
        }

        function hideGalleryFeedback() {
            if (!galleryFeedback) return;
            galleryFeedback.textContent = '';
            galleryFeedback.classList.add('hidden');
        }

        if (galleryDropzone) {
            galleryDropzone.addEventListener('click', () => {
                const currentTotal = getExistingCount() + stagedGalleryFiles.length;
                if (currentTotal < MAX_GALLERY_IMAGES) {
                    galleryInput.click();
                }
            });
            ['dragenter', 'dragover'].forEach(ev => {
                galleryDropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    const currentTotal = getExistingCount() + stagedGalleryFiles.length;
                    if (currentTotal < MAX_GALLERY_IMAGES) {
                        galleryDropzone.classList.add('border-blue-500', 'bg-blue-100/50');
                    }
                });
            });
            ['dragleave', 'drop'].forEach(ev => {
                galleryDropzone.addEventListener(ev, (e) => {
                    e.preventDefault();
                    galleryDropzone.classList.remove('border-blue-500', 'bg-blue-100/50');
                });
            });
            galleryDropzone.addEventListener('drop', (e) => {
                if (e.dataTransfer.files && e.dataTransfer.files.length) {
                    handleNewGalleryFiles(e.dataTransfer.files);
                }
            });
        }

        if (galleryGrid) {
            ['dragenter', 'dragover'].forEach(ev => {
                galleryGrid.addEventListener(ev, (e) => {
                    e.preventDefault();
                });
            });
            galleryGrid.addEventListener('drop', (e) => {
                e.preventDefault();
                if (e.dataTransfer.files && e.dataTransfer.files.length) {
                    handleNewGalleryFiles(e.dataTransfer.files);
                }
            });
        }

        if (galleryInput) {
            galleryInput.addEventListener('change', function() {
                if (this.files && this.files.length) {
                    handleNewGalleryFiles(this.files);
                }
            });
        }

        if (galleryClearNewBtn) {
            galleryClearNewBtn.addEventListener('click', () => {
                stagedGalleryFiles = [];
                hideGalleryFeedback();
                renderStagedGallery();
            });
        }

        updateGalleryCounter();
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: '#description',
            height: 380,
            menubar: false,
            plugins: 'lists link code wordcount',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | removeformat | code',
            branding: false,
            promotion: false,
            statusbar: true,
            content_style: 'body { font-family: "Figtree", system-ui, -apple-system, sans-serif; font-size: 14px; line-height: 1.7; color: #1e293b; padding: 14px; } h2, h3 { font-weight: 700; color: #0f172a; margin-top: 1.25rem; margin-bottom: 0.5rem; } ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 0.75rem; } li { margin-bottom: 0.25rem; } p { margin-bottom: 0.75rem; } strong { font-weight: 700; color: #0f172a; }',
            setup: function (editor) {
                editor.on('change keyup', function () {
                    editor.save();
                });
            }
        });

        // --- AI Description Generator ---
        (() => {
            const btn = document.getElementById('btnAiGenerate');
            const indicator = document.getElementById('ai-status-indicator');
            const statusText = document.getElementById('ai-status-text');

            if (!btn) return;

            btn.addEventListener('click', async () => {
                const titleInput = document.getElementById('title');
                const title = titleInput ? titleInput.value.trim() : '';

                if (!title) {
                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.error('Vul eerst de productnaam in zodat AI het product kan opzoeken.');
                    } else {
                        alert('Vul eerst de productnaam in zodat AI het product kan opzoeken.');
                    }
                    titleInput?.focus();
                    return;
                }

                const brand = document.getElementById('brand')?.value.trim() || '';
                const sku = document.getElementById('sku')?.value.trim() || '';
                const price = document.getElementById('price')?.value.trim() || '';
                const categorySelect = document.getElementById('category_id');
                const categoryName = categorySelect && categorySelect.selectedIndex >= 0 ? categorySelect.options[categorySelect.selectedIndex].text : '';
                
                const featureInputs = [...document.querySelectorAll('input[name="features[]"]')];
                const features = featureInputs.map(i => i.value.trim()).filter(Boolean);

                const editor = tinymce.get('description');
                const contentText = editor ? editor.getContent({ format: 'text' }).trim() : '';
                if (contentText.length > 0) {
                    if (!confirm('De beschrijving bevat al tekst. Wil je deze overschrijven met de AI-gegenereerde beschrijving?')) {
                        return;
                    }
                }

                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                if (indicator) {
                    indicator.classList.remove('hidden');
                    if (statusText) statusText.textContent = 'Zoeken naar specificaties op het internet...';
                }

                const stepTimer = setTimeout(() => {
                    if (statusText) statusText.textContent = 'AI schrijft een professionele SEO-beschrijving...';
                }, 1400);

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.content;
                    const res = await fetch('{{ route("admin.webshop.products.generate-description") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            title,
                            brand,
                            sku,
                            price,
                            category_name: categoryName,
                            features,
                            enable_search: true,
                        })
                    });

                    const data = await res.json();
                    clearTimeout(stepTimer);

                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Er is een fout opgetreden.');
                    }

                    if (editor) {
                        editor.setContent(data.description);
                        editor.save();
                    } else {
                        const el = document.getElementById('description');
                        if (el) el.value = data.description;
                    }

                    const sourcesCount = data.search_count ?? 0;
                    const msg = sourcesCount > 0 
                        ? `Beschrijving gegenereerd (${sourcesCount} webbronnen geraadpleegd)!` 
                        : 'Beschrijving succesvol gegenereerd!';

                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.success(msg);
                    } else {
                        alert(msg);
                    }
                } catch (err) {
                    clearTimeout(stepTimer);
                    if (window.SlimmePC && window.SlimmePC.toast) {
                        window.SlimmePC.toast.error(err.message);
                    } else {
                        alert(err.message);
                    }
                } finally {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                    if (indicator) indicator.classList.add('hidden');
                }
            });
        })();
    </script>
</x-admin.layout>
