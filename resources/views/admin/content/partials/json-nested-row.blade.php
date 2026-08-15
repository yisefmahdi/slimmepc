<div class="sm:col-span-2 rounded-xl border p-4 bg-white/60 dark:bg-slate-800/20"
     data-nested-block data-nested-key="{{ $field['key'] }}"
     style="border-color: rgba(59,130,246,.3)">
    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
        <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider" style="color: var(--c-heading)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-blue-600">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5M3.75 14.25h16.5M3.75 6.75h16.5M3.75 17.25h16.5" />
            </svg>
            {{ $field['label'] }}
        </span>
        <button type="button" data-add-nested-row
                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-200 dark:bg-blue-900/40 dark:text-blue-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Rij toevoegen
        </button>
    </div>

    <div class="json-nested-rows space-y-3">
        @forelse ($items as $nestedIndex => $nestedItem)
            @include('admin.content.partials.json-nested-row-item', [
                'page' => $page, 'section' => $section, 'blockKey' => $blockKey,
                'block' => $block, 'field' => $field, 'parentIndex' => $parentIndex,
                'nestedIndex' => $nestedIndex, 'item' => (array) $nestedItem,
            ])
        @empty
            @include('admin.content.partials.json-nested-row-item', [
                'page' => $page, 'section' => $section, 'blockKey' => $blockKey,
                'block' => $block, 'field' => $field, 'parentIndex' => $parentIndex,
                'nestedIndex' => 0, 'item' => [],
            ])
        @endforelse
    </div>

    <template data-nested-row-template>
        @include('admin.content.partials.json-nested-row-item', [
            'page' => $page, 'section' => $section, 'blockKey' => $blockKey,
            'block' => $block, 'field' => $field, 'parentIndex' => $parentIndex,
            'nestedIndex' => '__NINDEX__', 'item' => [],
        ])
    </template>
</div>