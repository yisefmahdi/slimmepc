<x-admin.layout title="Inkoop & Verkoop">
    <div class="flex h-[calc(100dvh-108px)] min-h-[24rem] flex-col overflow-hidden lg:h-[calc(100dvh-9rem)] lg:min-h-[26rem]">

        {{-- Header --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Inkoop &amp; Verkoop</h2>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Overzicht van ingekochte en verkochte producten.</p>
            </div>
            <a href="{{ route('admin.purchase-sales.create') }}"
               class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nieuw bestel
            </a>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden rounded-2xl border" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            {{-- Toolbar --}}
            <div class="shrink-0 border-b px-4 py-3" style="border-color: rgba(148,163,184,.15)">
                <form method="GET" action="{{ route('admin.purchase-sales.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <input type="month" name="month" value="{{ $month }}" aria-label="Filter op maand"
                           class="h-9 shrink-0 rounded-lg border px-2 text-xs outline-none sm:w-44"
                           style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                    <button type="submit" class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 px-4 text-xs font-bold text-white transition hover:bg-blue-700">Toepassen</button>
                    @if($month)
                        <a href="{{ route('admin.purchase-sales.index') }}" class="inline-flex h-9 shrink-0 items-center justify-center rounded-lg border px-4 text-xs font-semibold" style="color: var(--c-heading); border-color: var(--c-input-border)">Wissen</a>
                    @endif
                </form>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                    <span class="rounded-full bg-blue-50 px-3 py-1.5 font-bold text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">Inkoop: € {{ number_format($records->sum('purchase_price'), 2, ',', '.') }}</span>
                    <span class="rounded-full bg-green-50 px-3 py-1.5 font-bold text-green-600 dark:bg-green-900/30 dark:text-green-400">Verkoop: € {{ number_format($records->sum('sale_price'), 2, ',', '.') }}</span>
                    <span class="rounded-full bg-purple-50 px-3 py-1.5 font-bold text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">Winst: € {{ number_format($records->sum(fn($r) => $r->profit ?? 0), 2, ',', '.') }}</span>
                    @if($month)
                        <span class="rounded-full bg-slate-100 px-3 py-1.5 font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ \Carbon\Carbon::parse($month)->format('F Y') }}</span>
                    @endif
                </div>
            </div>

            {{-- Table --}}
            <div class="min-h-0 flex-1 overflow-auto w-full" style="-webkit-overflow-scrolling: touch;">
                <table class="w-full border-collapse text-left" style="min-width: 1080px;">
                    <thead class="sticky top-0 z-20" style="background-color: var(--c-card)">
                        <tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Product</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Leverancier</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Inkoopprijs</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Klant</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Verkoopprijs</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Winst</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Inkoopdatum</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Verkoopdatum</th>
                            <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Status</th>
                            <th class="w-[100px] px-3 py-3 text-right text-xs font-bold uppercase tracking-wide whitespace-nowrap sticky right-0 z-10" style="color: var(--c-muted); background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color:rgba(148,163,184,.12)">
                                <td class="px-3 py-3 text-sm font-semibold" style="color:var(--c-heading)">{{ $record->product_name }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap" style="color:var(--c-heading)">{{ $record->supplier_name }}</td>
                                <td class="px-3 py-3 text-sm font-semibold whitespace-nowrap" style="color:var(--c-heading)">€ {{ number_format($record->purchase_price, 2, ',', '.') }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap" style="color:var(--c-heading)">{{ $record->customer_name ?? '—' }}</td>
                                <td class="px-3 py-3 text-sm whitespace-nowrap" style="color:var(--c-heading)">@if($record->sale_price)€ {{ number_format($record->sale_price, 2, ',', '.') }}@else<span style="color:var(--c-muted)">—</span>@endif</td>
                                <td class="px-3 py-3 text-sm font-bold whitespace-nowrap">@if($record->profit !== null)<span class="text-green-600">€ {{ number_format($record->profit, 2, ',', '.') }}</span>@else<span style="color:var(--c-muted)">—</span>@endif</td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">{{ $record->purchase_date instanceof \Carbon\Carbon ? $record->purchase_date->format('d-m-Y') : $record->purchase_date }}</td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap" style="color:var(--c-muted)">{{ $record->sale_date ? ($record->sale_date instanceof \Carbon\Carbon ? $record->sale_date->format('d-m-Y') : $record->sale_date) : '—' }}</td>
                                <td class="px-3 py-3 whitespace-nowrap">
                                    @if($record->sale_price)
                                        <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Verkocht</span>
                                    @else
                                        <span class="inline-block rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-700">Niet verkocht</span>
                                    @endif
                                </td>
                                <td class="w-[100px] px-3 py-2 text-right sticky right-0" style="background-color: var(--c-card); box-shadow: -8px 0 12px -4px rgba(15,23,42,.06);">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.purchase-sales.edit', $record->id) }}" title="Bewerken" class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400" style="color: var(--c-muted)"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg></a>
                                        <button type="button" onclick="askDeleteRecord({{ $record->id }})" title="Verwijderen" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="px-3 py-12 text-center text-sm" style="color: var(--c-muted)">Geen records gevonden.</td></tr>
                        @endforelse
                    </tbody>
                    @if($records->count())
                        <tfoot class="sticky bottom-0" style="background-color: var(--c-card)">
                            <tr class="border-t-2 bg-blue-50/60 font-semibold" style="border-color: rgba(148,163,184,.25)">
                                <td colspan="2" class="px-3 py-3 text-right text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Totaal:</td>
                                <td class="px-3 py-3 text-sm font-bold whitespace-nowrap" style="color:var(--c-heading)">€ {{ number_format($records->sum('purchase_price'), 2, ',', '.') }}</td>
                                <td></td>
                                <td class="px-3 py-3 text-sm font-bold whitespace-nowrap" style="color:var(--c-heading)">€ {{ number_format($records->sum('sale_price'), 2, ',', '.') }}</td>
                                <td class="px-3 py-3 text-sm font-bold whitespace-nowrap text-green-600">€ {{ number_format($records->sum(fn($r) => $r->profit ?? 0), 2, ',', '.') }}</td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <x-admin.modal id="recordDeleteModal" title="Record verwijderen" size="sm">
        <div class="flex items-start gap-4">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg></span>
            <div>
                <p class="text-sm font-semibold" style="color: var(--c-heading)">Weet je zeker dat je dit record wilt verwijderen?</p>
                <p class="mt-1 text-xs leading-5" style="color: var(--c-muted)">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold hover:bg-slate-100" style="color: var(--c-heading); border-color: var(--c-input-border)">Annuleren</button>
            <form id="recordDeleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white hover:bg-red-700">Ja, verwijderen</button>
            </form>
        </x-slot>
    </x-admin.modal>

    <script>
        function askDeleteRecord(id) {
            document.getElementById('recordDeleteForm').action = '{{ url('admin/purchase-sales') }}/' + id;
            window.SlimmePC.modal.open('recordDeleteModal');
        }
    </script>
</x-admin.layout>
