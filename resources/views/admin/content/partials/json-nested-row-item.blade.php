<div class="json-nested-row rounded-xl border p-3.5 shadow-sm"
     style="border-color: rgba(148,163,184,.22); background-color: var(--c-card, #ffffff)">
    <div class="mb-2.5 flex items-center justify-between border-b pb-2" style="border-color: rgba(148,163,184,.15)">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold" style="color: var(--c-heading)">
            <span class="flex h-5 min-w-5 items-center justify-center rounded-md bg-blue-100 px-1.5 text-[11px] font-black text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                #<span data-nested-row-number>{{ is_numeric($nestedIndex) ? sprintf('%02d', $nestedIndex + 1) : '01' }}</span>
            </span>
            Prijs
        </span>
        <button type="button" data-remove-nested-row
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
            Verwijderen
        </button>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach ($field['fields'] ?? [] as $subField)
            @php $subValue = $item[$subField['key']] ?? ''; @endphp
            <div class="{{ ($subField['type'] ?? 'text') === 'textarea' ? 'sm:col-span-2' : '' }}">
                <label class="mb-1 block text-[11px] font-semibold" style="color: var(--c-muted)">
                    {{ $subField['label'] }}
                </label>

                @if (($subField['type'] ?? 'text') === 'textarea')
                    <textarea name="blocks[{{ $blockKey }}][{{ $parentIndex }}][{{ $field['key'] }}][{{ $nestedIndex }}][{{ $subField['key'] }}]" rows="2"
                              class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ $subValue }}</textarea>
                @elseif (($subField['type'] ?? 'text') === 'icon')
                    <div class="icon-picker" data-icon-picker>
                        <input type="hidden" name="blocks[{{ $blockKey }}][{{ $parentIndex }}][{{ $field['key'] }}][{{ $nestedIndex }}][{{ $subField['key'] }}]" value="{{ $subValue }}">
                        <button type="button" class="icon-picker-trigger px-3 py-1.5">
                            <i data-lucide="{{ $subValue ?: 'circle' }}" class="icon-picker-preview h-4 w-4"></i>
                            <span class="icon-picker-name">{{ $subValue ?: 'Kies een pictogram' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 shrink-0 opacity-50">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div class="icon-picker-dropdown" hidden>
                            <input type="search" class="icon-picker-search" placeholder="Zoek pictogram...">
                            <div class="icon-picker-grid" data-icon-grid></div>
                        </div>
                    </div>
                @else
                    <input type="text" name="blocks[{{ $blockKey }}][{{ $parentIndex }}][{{ $field['key'] }}][{{ $nestedIndex }}][{{ $subField['key'] }}]" value="{{ $subValue }}"
                           class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                @endif
            </div>
        @endforeach
    </div>
</div>