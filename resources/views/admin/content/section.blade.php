<x-admin.layout :title="$title">
    @php
        $sectionInfos = [
            'header' => [
                'title' => 'Header & Logo bewerken',
                'desc' => 'Beheer hier het bedrijfslogo, de sitenaam en de tagline die bovenaan elke pagina in de navigatiebalk worden getoond.',
                'location' => 'Zichtbaar bovenaan de website (Header)'
            ],
            'hero' => [
                'title' => 'Hero (Hoofdbanner) bewerken',
                'desc' => 'De hoofdpagina banner van de homepage. Pas hier de pakkende titels, beschrijvingen en banner-afbeeldingen aan.',
                'location' => 'Bovenste grote sectie van de homepage'
            ],
            'why' => [
                'title' => 'Waarom voor ons kiezen bewerken',
                'desc' => 'Beheer de voordelenkaarten rondom de centrale hub en de statistieken onderaan deze sectie.',
                'location' => 'Tweede sectie op de homepage'
            ],
            'services' => [
                'title' => 'Onze diensten bewerken',
                'desc' => 'Bewerk de 8 servicekaarten van de homepage. Elke service kan via de schakelaar worden verborgen van de homepage (zonder hem te verwijderen).',
                'location' => 'Derde sectie op de homepage'
            ],
        ];

        $currentInfo = $sectionInfos[$sectionKey] ?? [
            'title' => $section['label'],
            'desc' => 'Beheer de inhoud van dit onderdeel op de homepage.',
            'location' => 'Homepage'
        ];
    @endphp

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3">

            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-extrabold tracking-tight sm:text-2xl" style="color: var(--c-heading)">
                        {{ $currentInfo['title'] }}
                    </h1>
                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-bold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        📍 {{ $currentInfo['location'] }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm mt-0.5" style="color: var(--c-muted)">
                    {{ $currentInfo['desc'] }}
                </p>
            </div>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="overflow-hidden rounded-2xl border shadow-sm"
         style="background-color: var(--c-card); border-color: rgba(148,163,184,.25)">
        <div class="bg-blue-50/50 px-4 py-4 border-b dark:bg-slate-800/40 flex items-center justify-between sm:px-6" style="border-color: rgba(148,163,184,.15)">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Inhoud &amp; Mediavelden ({{ count($section['blocks']) }} velden)</span>
            <span class="text-xs text-slate-500 italic">Wijzigingen worden direct opgeslagen</span>
        </div>
        <div class="px-4 py-5 sm:px-6 sm:py-6">
            <form class="section-form space-y-6"
                  data-url="{{ route('admin.content.section', ['page' => $page, 'section' => $sectionKey]) }}">

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 sm:gap-6">
                    @foreach ($section['blocks'] as $blockKey => $block)
                        @php $blockValue = $content[$sectionKey][$blockKey] ?? null; @endphp

                        @if (($block['type'] ?? 'text') === 'json')
                            {{-- Repeatable items --}}
                            <div class="sm:col-span-2 rounded-2xl border p-4 bg-slate-50/50 dark:bg-slate-800/20 sm:p-6" data-json-block data-block-key="{{ $blockKey }}" style="border-color: rgba(148,163,184,.25)">
                                <div class="mb-5 flex items-center justify-between gap-3">
                                    <div>
                                        <span class="flex items-center gap-2 text-sm font-bold uppercase tracking-wider" style="color: var(--c-heading)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-blue-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.75h16.5M3.75 14.25h16.5M3.75 6.75h16.5M3.75 17.25h16.5" />
                                            </svg>
                                            {{ $block['label'] }}
                                        </span>
                                        <span class="text-xs" style="color: var(--c-muted)">
                                            @if (!empty($block['fixed']))
                                                Bewerk hier de vaste items (toevoegen of verwijderen is niet mogelijk)
                                            @else
                                                Beheer hier de herhalende items (voeg toe of verwijder naar wens)
                                            @endif
                                        </span>
                                    </div>
                                    @if (empty($block['fixed']))
                                    <button type="button" data-add-row
                                            class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Item toevoegen
                                    </button>
                                    @endif
                                </div>

                                <div class="json-rows {{ !empty($block['columns']) && (int) $block['columns'] > 1 ? 'grid gap-4 sm:grid-cols-2' : 'space-y-4' }}">
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
                                <label class="mb-2 block text-xs font-bold uppercase tracking-wide" style="color: var(--c-heading)">
                                    {{ $block['label'] }}
                                    @if (!empty($block['hint']))
                                        <span class="font-normal normal-case text-slate-400"> &middot; {{ $block['hint'] }}</span>
                                    @endif
                                </label>

                                @if (($block['type'] ?? 'text') === 'textarea')
                                    <textarea name="blocks[{{ $blockKey }}]" rows="3"
                                              class="w-full rounded-xl border px-4 py-3 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40 shadow-sm"
                                              style="background-color: var(--c-input, rgba(0,0,0,.02)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">{{ old('blocks.'.$blockKey, $blockValue) }}</textarea>
                                @elseif (($block['type'] ?? 'text') === 'image')
                                    <div class="flex flex-wrap items-center gap-5 rounded-xl border p-4 bg-slate-50/50 dark:bg-slate-800/30" data-image-block style="border-color: rgba(148,163,184,.25)">
                                        <div class="flex items-center gap-4">
                                            <div class="relative h-20 w-28 overflow-hidden rounded-xl border bg-white dark:bg-slate-900 shadow-inner flex items-center justify-center" style="border-color: rgba(148,163,184,.3)">
                                                <img data-image-preview src="{{ $blockValue ? asset($blockValue) : '' }}"
                                                     alt="" class="h-full w-full object-contain p-1"
                                                     style="{{ $blockValue ? '' : 'display: none' }}">
                                                <span class="text-[10px] text-slate-400 absolute" style="{{ $blockValue ? 'display: none' : '' }}">Geen voorbeeld</span>
                                            </div>
                                            <div class="text-xs space-y-1" style="color: var(--c-muted)">
                                                <span class="block font-bold text-sm" style="color: var(--c-heading)" data-image-name>
                                                    @if ($blockValue)
                                                        {{ basename($blockValue) }}
                                                    @else
                                                        Geen afbeelding gekozen
                                                    @endif
                                                </span>
                                                <span class="block">Ondersteund: PNG, JPG, WEBP (Max 5MB).</span>
                                            </div>
                                        </div>
                                        <div class="min-w-[240px] flex-1">
                                            <input type="hidden" name="blocks[{{ $blockKey }}]" value="{{ $blockValue }}">
                                            <input type="file" name="blocks[{{ $blockKey }}_file]" accept="image/png,image/jpeg,image/webp"
                                                   class="block w-full cursor-pointer rounded-xl border text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100"
                                                   style="border-color: rgba(148,163,184,.3); color: var(--c-muted)">
                                        </div>
                                    </div>
                                @else
                                    <input type="text" name="blocks[{{ $blockKey }}]" value="{{ old('blocks.'.$blockKey, $blockValue) }}"
                                           class="w-full rounded-xl border px-4 py-3 text-sm outline-none transition focus:ring-2 focus:ring-blue-500/40 shadow-sm"
                                           style="background-color: var(--c-input, rgba(0,0,0,.02)); border-color: rgba(148,163,184,.3); color: var(--c-heading)">
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Action Footer --}}
                <div class="-mx-4 -mb-5 mt-8 flex items-center justify-between border-t px-4 py-4 bg-slate-50 dark:bg-slate-800/30 rounded-b-2xl sm:-mx-6 sm:-mb-6 sm:px-6"
                     style="border-color: rgba(148, 163, 184, 0.15)">
                    <span class="form-status text-xs font-bold"></span>
                    <x-admin.save-button :label="$section['label'] . ' opslaan'" />
                </div>
            </form>
        </div>
    </div>
</x-admin.layout>

