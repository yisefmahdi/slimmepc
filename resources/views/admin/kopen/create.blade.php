<x-admin.layout title="Nieuw bestel">
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight" style="color: var(--c-heading)">Nieuw bestel</h1>
            <p class="text-xs" style="color: var(--c-muted)">Voeg een nieuw inkoop- &amp; verkooprecord toe.</p>
        </div>
        <x-admin.back-button :href="route('admin.purchase-sales.index')" label="Overzicht" />
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

    <form action="{{ route('admin.purchase-sales.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Product &amp; Leverancier</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="product_name">Naam van het product <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="product_name" name="product_name" value="{{ old('product_name') }}" placeholder="Bijv. HP Laptop 15" />
                        @error('product_name')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="supplier_name">Leverancier <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" placeholder="Bijv. Tech BV" />
                        @error('supplier_name')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="purchase_date">Inkoopdatum <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="purchase_date" name="purchase_date" type="date" value="{{ old('purchase_date') }}" />
                        @error('purchase_date')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="purchase_price">Inkoopprijs (€) <span class="text-red-500">*</span></x-input-label>
                        <x-text-input id="purchase_price" name="purchase_price" type="number" step="0.01" min="0" value="{{ old('purchase_price') }}" placeholder="0.00" />
                        @error('purchase_price')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border shadow-sm" style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
            <div class="bg-blue-50/50 px-4 py-4 sm:px-6" style="border-bottom: 1px solid rgba(148,163,184,.15)">
                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600">Verkoop (optioneel)</h3>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="customer_name">Naam van de klant</x-input-label>
                        <x-text-input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" placeholder="Bijv. Jan Jansen" />
                        @error('customer_name')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="sale_date">Verkoopdatum</x-input-label>
                        <x-text-input id="sale_date" name="sale_date" type="date" value="{{ old('sale_date') }}" />
                        @error('sale_date')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <x-input-label for="sale_price">Verkoopprijs (€)</x-input-label>
                        <x-text-input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price') }}" placeholder="0.00" />
                        @error('sale_price')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="notes">Notities</x-input-label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Extra opmerkingen..." class="form-input w-full text-sm">{{ old('notes') }}</textarea>
                        @error('notes')<p class="mt-1 text-xs font-medium text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.purchase-sales.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border px-6 text-sm font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</a>
            <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-8 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] hover:-translate-y-0.5">Opslaan</button>
        </div>
    </form>
</x-admin.layout>
