<div class="json-row rounded-lg border p-4" style="border-color: rgba(148,163,184,.2); background-color: var(--c-input, rgba(0,0,0,.02))">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($block['fields'] as $field)
            @php $value = $item[$field['key']] ?? ''; @endphp

            <div>
                <label class="mb-1.5 block text-[11px] font-semibold " style="color: var(--c-muted)">
                    {{ $field['label'] }}
                </label>

                @if (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" rows="2"
                              class="w-full rounded-lg border px-2.5 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ $value }}</textarea>
                @elseif (($field['type'] ?? 'text') === 'boolean')
                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border px-2.5 py-1.5"
                           style="border-color: rgba(148,163,184,.3)">
                        <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="0">
                        <input type="checkbox" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="1"
                               @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN))
                               class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm" style="color: var(--c-heading)">Ja</span>
                    </label>
                @else
                    <input type="text" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}"
                           class="w-full rounded-lg border px-2.5 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-2 flex justify-end">
        <button type="button" data-remove-row
                class="rounded-lg px-2.5 py-1 text-xs font-bold text-red-500 transition hover:bg-red-50 dark:hover:bg-red-950/40">
            Verwijderen
        </button>
    </div>
</div>
