<div class="json-row rounded-xl border p-4 shadow-sm transition"
     style="border-color: rgba(148,163,184,.25); background-color: var(--c-card, #ffffff)">
    
    {{-- Row header --}}
    <div class="mb-3 flex items-center justify-between border-b pb-2.5" style="border-color: rgba(148,163,184,.15)">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold" style="color: var(--c-heading)">
            <span class="flex h-5 min-w-5 items-center justify-center rounded-md bg-blue-100 px-1.5 text-[11px] font-black text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                #<span data-row-number>{{ is_numeric($index) ? sprintf('%02d', $index + 1) : '01' }}</span>
            </span>
            Item
        </span>

        <button type="button" data-remove-row
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
            Verwijderen
        </button>
    </div>

    {{-- Fields grid --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach ($block['fields'] as $field)
            @php 
                $value = $item[$field['key']] ?? '';
                $isFull = ($field['type'] ?? 'text') === 'textarea';
            @endphp

            <div class="{{ $isFull ? 'sm:col-span-2' : '' }}">
                <label class="mb-1 block text-[11px] font-semibold" style="color: var(--c-muted)">
                    {{ $field['label'] }}
                </label>

                @if (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" rows="2"
                              class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ $value }}</textarea>
                @elseif (($field['type'] ?? 'text') === 'boolean')
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border px-3 py-1.5"
                           style="border-color: rgba(148,163,184,.3)">
                        <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="0">
                        <input type="checkbox" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="1"
                               @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN))
                               class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium" style="color: var(--c-heading)">Ja</span>
                    </label>
                @else
                    <input type="text" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}"
                           class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                @endif
            </div>
        @endforeach
    </div>
</div>

