<x-admin.layout :title="$title">
    {{-- Page tabs --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <nav class="flex flex-wrap gap-2">
            @foreach ($pages as $pageKey => $pageConfig)
                <a href="{{ route('admin.content.index', ['page' => $pageKey]) }}"
                   class="rounded-xl px-4 py-2 text-sm font-semibold transition duration-200
                          {{ $pageKey === $page
                                ? 'bg-gradient-to-r from-[#075be8] to-[#064bd7] text-white shadow-sm'
                                : 'border bg-white/70 hover:bg-white dark:bg-slate-900/70'
                            }}"
                   style="{{ $pageKey === $page ? '' : 'color: var(--c-heading); border-color: rgba(148,163,184,.25)' }}">
                    {{ $pageConfig['label'] }}
                </a>
            @endforeach
        </nav>

        <a href="{{ url('/') }}" target="_blank" class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">
            Website bekijken &rarr;
        </a>
    </div>

    {{-- Design settings accordion --}}
    <div class="mb-5 overflow-hidden rounded-2xl border shadow-sm"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)" x-data="{ open: false }">
        <button type="button" @click="open = !open"
                class="flex w-full items-center justify-between gap-4 px-5 py-4 text-start transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
            <span class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </span>
                <span>
                    <span class="block text-sm font-bold" style="color: var(--c-heading)">Ontwerp instellingen</span>
                    <span class="block text-xs" style="color: var(--c-muted)">Kleuren, lettertype en SEO van de hele website</span>
                </span>
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                 class="h-5 w-5 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: var(--c-muted)">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150"
             class="border-t px-5 py-5" style="border-color: rgba(148,163,184,.2)">
            <form id="design-form" data-url="{{ route('admin.content.design') }}" class="space-y-6">
                @foreach ($designGroups as $groupKey => $group)
                    <div>
                        <h3 class="mb-3 text-xs font-bold uppercase tracking-widest" style="color: var(--c-muted)">{{ $group['label'] }}</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($group['fields'] as $field)
                                @php
                                    $value = $design[$field['key']] ?? ($field['default'] ?? '');
                                @endphp
                                <div>
                                    <label class="mb-1.5 block text-xs font-semibold" style="color: var(--c-heading)">
                                        {{ $field['label'] }}
                                    </label>

                                    @if (($field['type'] ?? 'text') === 'textarea')
                                        <textarea name="design[{{ $groupKey }}][{{ $field['key'] }}]" rows="2"
                                                  class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                                  style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}</textarea>
                                    @elseif (($field['type'] ?? 'text') === 'select')
                                        <select name="design[{{ $groupKey }}][{{ $field['key'] }}]"
                                                class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                                style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                            @foreach ($field['options'] ?? [] as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                            @endforeach
                                        </select>
                                    @elseif (($field['type'] ?? 'text') === 'color')
                                        <div class="flex items-center gap-2">
                                            <input type="color" name="design[{{ $groupKey }}][{{ $field['key'] }}]" value="{{ $value }}"
                                                   class="h-10 w-14 cursor-pointer rounded-lg border p-1"
                                                   style="border-color: rgba(148,163,184,.3)">
                                            <input type="text" name="design[{{ $groupKey }}][{{ $field['key'] }}]_hex" value="{{ $value }}"
                                                   class="flex-1 rounded-xl border px-3 py-2 text-sm font-mono outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                                   style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                        </div>
                                    @else
                                        <input type="text" name="design[{{ $groupKey }}][{{ $field['key'] }}]" value="{{ old('design.'.$groupKey.'.'.$field['key'], $value) }}"
                                               class="w-full rounded-xl border px-3 py-2 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40"
                                               style="background-color: var(--c-input, rgba(0,0,0,.03)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="-mx-5 -mb-5 mt-6 flex items-center justify-between border-t px-5 py-4 bg-slate-50 dark:bg-slate-800/20 rounded-b-2xl"
                     style="border-color: rgba(148, 163, 184, 0.15)">
                    <span class="form-status text-xs font-semibold text-emerald-500"></span>
                    <x-admin.save-button label="Ontwerpinstellingen opslaan" />
                </div>
            </form>
        </div>
    </div>

    {{-- Per-section accordions --}}
    @foreach ($pages[$page]['sections'] as $sectionKey => $section)
        <div class="mb-4 overflow-hidden rounded-2xl border shadow-sm"
             style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)" x-data="{ open: false }">
            <button type="button" @click="open = !open"
                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-start transition hover:bg-slate-50 dark:hover:bg-slate-800/50">
                <span class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-sm font-bold" style="color: var(--c-heading)">{{ $section['label'] }}</span>
                        <span class="block text-xs" style="color: var(--c-muted)">Pagina: {{ $pages[$page]['label'] }} &middot; {{ count($section['blocks']) }} velden</span>
                    </span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     class="h-5 w-5 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''" style="color: var(--c-muted)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150"
                 class="border-t px-5 py-5" style="border-color: rgba(148,163,184,.2)">
                <form class="section-form space-y-5"
                      data-url="{{ route('admin.content.section', ['page' => $page, 'section' => $sectionKey]) }}">
                    @foreach ($section['blocks'] as $blockKey => $block)
                        @php $blockValue = $content[$sectionKey][$blockKey] ?? null; @endphp

                        @if (($block['type'] ?? 'text') === 'json')
                            {{-- Repeatable items --}}
                            <div class="rounded-xl border p-4" data-json-block style="border-color: rgba(148,163,184,.2)">
                                <div class="mb-3 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-widest" style="color: var(--c-muted)">
                                        {{ $block['label'] }}
                                    </span>
                                    <button type="button" data-add-row
                                            class="rounded-lg border border-blue-500/40 px-3 py-1.5 text-xs font-bold text-blue-600 transition hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-950/40">
                                        + Item toevoegen
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
                            <div>
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
                                                 alt="" class="h-14 w-20 rounded-lg border object-cover"
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

                    <div class="-mx-5 -mb-5 mt-6 flex items-center justify-between border-t px-5 py-4 bg-slate-50 dark:bg-slate-800/20 rounded-b-2xl"
                         style="border-color: rgba(148, 163, 184, 0.15)">
                        <span class="form-status text-xs font-semibold text-emerald-500"></span>
                        <x-admin.save-button :label="$section['label'] . ' opslaan'" />
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</x-admin.layout>
