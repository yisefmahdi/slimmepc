<x-admin.layout :title="$title">
    @php
        $sectionMeta = [
            'header' => ['desc' => 'Logo en naam bovenaan de website'],
            'hero'   => ['desc' => 'Grote introductiesectie met afbeeldingen'],
            'why'    => ['desc' => 'Voordelen en statistieken van de homepage'],
        ];
        $meta = $sectionMeta[$sectionKey] ?? ['desc' => 'Inhoud van deze section'];
    @endphp

    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight sm:text-2xl" style="color: var(--c-heading)">
                {{ $section['label'] }}
            </h2>
            <p class="text-xs" style="color: var(--c-muted)">Home-page &middot; {{ $meta['desc'] }}</p>
        </div>

        <a href="{{ url('/') }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40"
           style="border-color: rgba(96,165,250,.35)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
            </svg>
            Website bekijken
        </a>
    </div>

    {{-- Main form card --}}
    <div class="overflow-hidden rounded-2xl border shadow-sm"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="px-5 py-5">
            <form class="section-form space-y-5"
                  data-url="{{ route('admin.content.section', ['page' => $page, 'section' => $sectionKey]) }}">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    @foreach ($section['blocks'] as $blockKey => $block)
                        @php $blockValue = $content[$sectionKey][$blockKey] ?? null; @endphp

                        @if (($block['type'] ?? 'text') === 'json')
                            {{-- Repeatable items --}}
                            <div class="sm:col-span-2 rounded-xl border p-4 sm:p-5" data-json-block style="border-color: rgba(148,163,184,.2)">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <span class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest" style="color: var(--c-muted)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5M3.75 14.25h16.5M3.75 6.75h16.5M3.75 17.25h16.5" />
                                        </svg>
                                        {{ $block['label'] }}
                                    </span>
                                    <button type="button" data-add-row
                                            class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border border-blue-500/40 px-3 py-1.5 text-xs font-bold text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Item toevoegen
                                    </button>
                                </div>

                                <div class="json-rows {{ !empty($block['columns']) && (int) $block['columns'] > 1 ? 'grid gap-3 sm:grid-cols-2' : 'space-y-3' }}">
                                    @forelse ((array) $blockValue as $index => $item)
                                        @include('admin.content.partials.json-row', [
                                            'page' => $page, 'section' => $sectionKey, 'blockKey' => $blockKey,
                                            'block' => $block, 'item' => (array) $item, 'index' => $index,
                                        ])
                                    @empty
                                        @include('admin.content.partials.json-row', [
                                            'page' => $page, 'section' => $sectionKey, 'blockKey' => $blockKey,
                                            'block' => $block, 'item' => [], 'index' => 0,
                                        ])
                                    @endforelse
                                </div>

                                <template data-row-template>
                                    @include('admin.content.partials.json-row', [
                                        'page' => $page, 'section' => $sectionKey, 'blockKey' => $blockKey,
                                        'block' => $block, 'item' => [], 'index' => '__INDEX__',
                                    ])
                                </template>
                            </div>
                        @else
                            {{-- Single field --}}
                            <div class="{{ (($block['type'] ?? 'text') === 'textarea' || ($block['type'] ?? 'text') === 'image') ? 'sm:col-span-2' : '' }}">
                                <label class="mb-1.5 block text-xs font-semibold" style="color: var(--c-heading)">
                                    {{ $block['label'] }}
                                    @if (!empty($block['hint']))
                                        <span class="font-normal text-slate-400"> &middot; {{ $block['hint'] }}</span>
                                    @endif
                                </label>

                                @if (($block['type'] ?? 'text') === 'textarea')
                                    <textarea name="blocks[{{ $blockKey }}]" rows="3"
                                              class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                              style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ old('blocks.'.$blockKey, $blockValue) }}</textarea>
                                @elseif (($block['type'] ?? 'text') === 'image')
                                    <div class="flex flex-wrap items-center gap-4" data-image-block>
                                        <div class="flex items-center gap-3">
                                            <img data-image-preview src="{{ $blockValue ? asset($blockValue) : '' }}"
                                                 alt="" class="h-16 w-24 rounded-lg border object-cover"
                                                 style="border-color: rgba(148,163,184,.3); {{ $blockValue ? '' : 'display: none' }}">
                                            <div class="text-xs" style="color: var(--c-muted)">
                                                <span class="block font-semibold" style="color: var(--c-heading)" data-image-name>
                                                    @if ($blockValue)
                                                        {{ basename($blockValue) }}
                                                    @else
                                                        Geen afbeelding gekozen
                                                    @endif
                                                </span>
                                                <span class="block">Huidige afbeelding blijft behouden als je niets kiest.</span>
                                            </div>
                                        </div>
                                        <div class="min-w-52 flex-1">
                                            <input type="hidden" name="blocks[{{ $blockKey }}]" value="{{ $blockValue }}">
                                            <input type="file" name="blocks[{{ $blockKey }}_file]" accept="image/png,image/jpeg,image/webp"
                                                   class="block w-full cursor-pointer rounded-xl border text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100"
                                                   style="border-color: rgba(148,163,184,.3); color: var(--c-muted)">
                                        </div>
                                    </div>
                                @else
                                    <input type="text" name="blocks[{{ $blockKey }}]" value="{{ old('blocks.'.$blockKey, $blockValue) }}"
                                           class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                           style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="-mx-5 -mb-5 mt-6 flex items-center justify-between border-t px-5 py-4 bg-slate-50 dark:bg-slate-800/20 rounded-b-2xl"
                     style="border-color: rgba(148, 163, 184, 0.15)">
                    <span class="form-status text-xs font-semibold text-emerald-500"></span>
                    <x-admin.save-button :label="$section['label'] . ' opslaan'" />
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>
