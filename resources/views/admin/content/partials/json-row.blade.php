<div class="json-row rounded-xl border p-3 transition"
     style="border-color: rgba(148,163,184,.25); background-color: var(--c-card, #ffffff)">
    
    {{-- Row header --}}
    <div class="mb-3 flex items-center justify-between border-b pb-2.5" style="border-color: rgba(148,163,184,.15)">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold" style="color: var(--c-heading)">
            <span class="flex h-5 min-w-5 items-center justify-center rounded-md bg-blue-100 px-1.5 text-[11px] font-black text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                #<span data-row-number>{{ is_numeric($index) ? sprintf('%02d', $index + 1) : '01' }}</span>
            </span>
            Item
        </span>

        @if (empty($block['fixed']))
        <button type="button" data-remove-row
                class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-bold text-red-500 transition hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
            Verwijderen
        </button>
        @else
        @php
            $boolField = null;
            foreach ($block['fields'] ?? [] as $f) {
                if (($f['type'] ?? 'text') === 'boolean') { $boolField = $f; break; }
            }
        @endphp
        @if ($boolField)
        <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border px-2.5 py-1 transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
               style="border-color: rgba(148,163,184,.25)">
            <span class="text-xs font-bold" style="color: var(--c-heading)">{{ $boolField['label'] }}</span>
            <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $boolField['key'] }}]" value="0">
            <input type="checkbox" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $boolField['key'] }}]" value="1"
                   @checked(filter_var($item[$boolField['key']] ?? '', FILTER_VALIDATE_BOOLEAN))
                   class="peer sr-only">
            <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition-colors duration-200 peer-checked:bg-green-500 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-md after:transition-transform after:duration-200 peer-checked:after:translate-x-5"></span>
        </label>
        @else
        <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300">
            Vast item
        </span>
        @endif
        @endif
    </div>

    {{-- Fields grid --}}
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        @foreach ($block['fields'] as $field)
            @php 
                $value = $item[$field['key']] ?? '';
                $isFull = in_array($field['type'] ?? 'text', ['textarea', 'boolean', 'image', 'nested']);
            @endphp

            @if (!empty($field['hidden']))
                <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}">
            @elseif (!empty($block['fixed']) && ($field['type'] ?? 'text') === 'boolean')
                {{-- Toggle rendered in the row header (see Vast item / Verberg area) --}}
            @elseif (($field['type'] ?? 'text') === 'nested')
                @include('admin.content.partials.json-nested-row', [
                    'page' => $page, 'section' => $section, 'blockKey' => $blockKey,
                    'block' => $block, 'parentIndex' => $index, 'field' => $field,
                    'items' => (array) ($item[$field['key']] ?? []),
                ])
            @else
            <div class="{{ $isFull ? 'md:col-span-2' : '' }}">
                <label class="mb-1 block text-[11px] font-semibold" style="color: var(--c-muted)">
                    {{ $field['label'] }}
                </label>

                @if (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" rows="2"
                              class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ $value }}</textarea>
                @elseif (($field['type'] ?? 'text') === 'boolean')
                    <label class="inline-flex cursor-pointer items-center rounded-xl border px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                           style="border-color: rgba(148,163,184,.3)">
                        <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="0">
                        <input type="checkbox" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="1"
                               @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN))
                               class="peer sr-only">
                        <span class="relative h-6 w-11 shrink-0 rounded-full bg-slate-300 transition-colors duration-200 peer-checked:bg-green-500 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-md after:transition-transform after:duration-200 peer-checked:after:translate-x-5"></span>
                    </label>
@elseif (($field['type'] ?? 'text') === 'image')
                                    <div class="flex flex-wrap items-center gap-4 rounded-xl border p-4 bg-slate-50/50 dark:bg-slate-800/30" data-image-block style="border-color: rgba(148,163,184,.25)">
                                        <div class="flex items-center gap-4">
                                            <div class="relative h-20 w-28 overflow-hidden rounded-xl border bg-white dark:bg-slate-900 shadow-inner flex items-center justify-center" style="border-color: rgba(148,163,184,.3)">
                                                <img data-image-preview src="{{ $value ? asset('assets/img/landing/' . $value) : '' }}"
                                                     alt="" class="h-full w-full object-contain p-1"
                                                     style="{{ $value ? '' : 'display: none' }}">
                                                <span class="text-[10px] text-slate-400 absolute" style="{{ $value ? 'display: none' : '' }}">Geen voorbeeld</span>
                                            </div>
                                            <div class="text-xs space-y-1" style="color: var(--c-muted)">
                                                <span class="block">Ondersteund: PNG, JPG, WEBP (Max 5MB).</span>
                                            </div>
                                        </div>
                                        <div class="min-w-[240px] flex-1">
                                            <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}">
                                            <input type="file" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}_file]" accept="image/png,image/jpeg,image/webp"
                                                   class="block w-full cursor-pointer rounded-xl border text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100"
                                                   style="border-color: rgba(148,163,184,.3); color: var(--c-muted)">
                                        </div>
                                    </div>
                                @elseif (($field['type'] ?? 'text') === 'icon')
                                    <div class="icon-picker" data-icon-picker>
                                        <input type="hidden" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}">
                                        <button type="button" class="icon-picker-trigger px-3 py-1.5">
                                            <i data-lucide="{{ $value ?: 'circle' }}" class="icon-picker-preview h-4 w-4"></i>
                                            <span class="icon-picker-name">{{ $value ?: 'Kies een pictogram' }}</span>
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
                    <input type="text" name="blocks[{{ $blockKey }}][{{ $index }}][{{ $field['key'] }}]" value="{{ $value }}"
                           class="w-full rounded-xl border px-3 py-1.5 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                @endif
            </div>
            @endif
        @endforeach
    </div>
</div>

