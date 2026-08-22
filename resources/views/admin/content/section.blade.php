<x-admin.layout :title="$title">
    @php
        $sectionInfos = [
            'home' => [
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
                    'desc' => 'Beheer de servicekaarten van de homepage. Voeg nieuwe diensten toe, verwijder ze of verberg een dienst tijdelijk van de homepage via de schakelaar (zonder hem te verwijderen).',
                    'location' => 'Derde sectie op de homepage'
                ],
                'footer' => [
                    'title' => 'Footer bewerken',
                    'desc' => 'Beheer de bedrijfstekst, socialmedia-links, kolomlinks, contactgegevens, trustbadges, copyright en betaalmethoden onderaan elke pagina.',
                    'location' => 'Onderaan elke pagina (Footer)'
                ],
            ],
            'tarieven' => [
                'hero' => [
                    'title' => 'Hero (Tarieven) bewerken',
                    'desc' => 'De banner bovenaan de tarievenpagina. Pas de badge, titels, beschrijving, knoppen, afbeelding en vertrouwenspunten aan.',
                    'location' => 'Bovenste grote sectie van de tarievenpagina'
                ],
                'pricing' => [
                    'title' => 'Tarieven & prijzen bewerken',
                    'desc' => 'Beheer de categorieën (tabs) met hun eigen prijslijsten. Voeg categorieën toe, verwijder ze en bewerk de prijzen per categorie.',
                    'location' => 'Tweede sectie van de tarievenpagina'
                ],
                'extra' => [
                    'title' => 'Algemene & zakelijke tarieven bewerken',
                    'desc' => 'Beheer de twee accordions (Algemene tarieven en Zakelijke IT-service) met hun prijzen, plus de vier trustkaarten onderaan.',
                    'location' => 'Derde sectie van de tarievenpagina'
                ],
            ],
            'contact' => [
                'hero' => [
                    'title' => 'Hero (Contact) bewerken',
                    'desc' => 'De banner bovenaan de contactpagina. Pas de badge, titels, beschrijving, knoppen, WhatsApp-nummer, afbeelding en vertrouwenspunten aan.',
                    'location' => 'Bovenste grote sectie van de contactpagina'
                ],
                'gegevens' => [
                    'title' => 'Contactgegevens bewerken',
                    'desc' => 'Beheer de drie kaarten met bedrijfsgegevens, contactmethoden (telefoon, e-mail, WhatsApp) en openingstijden.',
                    'location' => 'Tweede sectie van de contactpagina'
                ],
                'formulier' => [
                    'title' => 'Contactformulier bewerken',
                    'desc' => 'Beheer de introductietekst, titels, beschrijving en de voordelen naast het contactformulier. (De formuliervelden zelf zijn vaste opmaak.)',
                    'location' => 'Derde sectie van de contactpagina'
                ],
                'locatie' => [
                    'title' => 'Locatie & route bewerken',
                    'desc' => 'Beheer de tekst naast de kaart, de ingebedde Google Maps-kaart, de route-knop en de locatiepunten.',
                    'location' => 'Vierde sectie van de contactpagina'
                ],
            ],
            'overons' => [
                'hero' => [
                    'title' => 'Hero (Over ons) bewerken',
                    'desc' => 'De banner bovenaan de over-ons-pagina. Pas de badge, titels, beschrijving, achtergrondafbeelding, vertrouwenspunten en de Google-beoordeling aan.',
                    'location' => 'Bovenste grote sectie van de over-ons-pagina'
                ],
                'meet' => [
                    'title' => 'Meet Mo bewerken',
                    'desc' => 'Beheer de foto en het verhaal van de eigenaar, de punten waar we voor staan en de handtekening onderaan.',
                    'location' => 'Tweede sectie van de over-ons-pagina'
                ],
                'why' => [
                    'title' => 'Waarom klanten terugkomen bewerken',
                    'desc' => 'Beheer de vier kaarten met de redenen waarom klanten voor Slimme-PC kiezen.',
                    'location' => 'Derde sectie van de over-ons-pagina'
                ],
                'werkplaats' => [
                    'title' => 'Binnen in onze werkplaats bewerken',
                    'desc' => 'Beheer de foto\'s van de werkplaats. De kaarten schuiven horizontaal (links/rechts) op de pagina.',
                    'location' => 'Vierde sectie van de over-ons-pagina'
                ],
                'reis' => [
                    'title' => 'Onze reis bewerken',
                    'desc' => 'Beheer de mijlpalen op de tijdlijn (jaartal, pictogram en titel).',
                    'location' => 'Vijfde sectie van de over-ons-pagina'
                ],
                'reviews' => [
                    'title' => 'Wat klanten zeggen bewerken',
                    'desc' => 'Beheer de klantbeoordelingen (sterren, naam en tekst) die op de over-ons-pagina worden getoond.',
                    'location' => 'Zesde sectie van de over-ons-pagina'
                ],
                'trust' => [
                    'title' => 'Trust onderaan bewerken',
                    'desc' => 'Beheer de vier vertrouwenspunten (pictogram, titel en ondertitel) onderaan de over-ons-pagina.',
                    'location' => 'Zevende sectie van de over-ons-pagina'
                ],
            ],
        ];

        $currentInfo = $sectionInfos[$page][$sectionKey] ?? [
            'title' => $section['label'],
            'desc' => 'Beheer de inhoud van dit onderdeel van de ' . ($pageLabel ?? $page) . ' pagina.',
            'location' => $pageLabel ?? $page
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

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 md:gap-6">
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

                                <div class="json-rows {{ !empty($block['columns']) && (int) $block['columns'] > 1 ? 'grid gap-4 md:grid-cols-2' : 'space-y-4' }}">
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
                                @elseif (($block['type'] ?? 'text') === 'video')
                                    <div class="flex flex-wrap items-center gap-5 rounded-xl border p-4 bg-slate-50/50 dark:bg-slate-800/30" data-video-block style="border-color: rgba(148,163,184,.25)">
                                        <div class="flex items-center gap-4">
                                            <div class="relative h-24 w-40 overflow-hidden rounded-xl border bg-white dark:bg-slate-900 shadow-inner flex items-center justify-center" style="border-color: rgba(148,163,184,.3)">
                                                <video data-video-preview src="{{ $blockValue ? asset($blockValue) : '' }}"
                                                       controls class="h-full w-full object-contain p-1"
                                                       style="{{ $blockValue ? '' : 'display: none' }}"></video>
                                                <span class="text-[10px] text-slate-400 absolute" style="{{ $blockValue ? 'display: none' : '' }}">Geen video</span>
                                            </div>
                                            <div class="text-xs space-y-1" style="color: var(--c-muted)">
                                                <span class="block font-bold text-sm" style="color: var(--c-heading)" data-video-name>
                                                    @if ($blockValue)
                                                        {{ basename($blockValue) }}
                                                    @else
                                                        Geen video gekozen
                                                    @endif
                                                </span>
                                                <span class="block">Ondersteund: MP4, MOV, WEBM (Max 50MB).</span>
                                            </div>
                                        </div>
                                        <div class="min-w-[240px] flex-1">
                                            <input type="hidden" name="blocks[{{ $blockKey }}]" value="{{ $blockValue }}" data-video-value>
                                            <input type="file" name="blocks[{{ $blockKey }}_file]" accept="video/mp4,video/webm,video/quicktime"
                                                   class="js-media-input block w-full cursor-pointer rounded-xl border text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:text-sm file:font-bold file:text-blue-700 hover:file:bg-blue-100"
                                                   style="border-color: rgba(148,163,184,.3); color: var(--c-muted)"
                                                   data-media-type="video">
                                            <div class="js-media-progress mt-2 hidden">
                                                <div class="h-2 w-full overflow-hidden rounded bg-slate-200">
                                                    <div class="js-media-bar h-full bg-blue-600" style="width:0%"></div>
                                                </div>
                                                <p class="js-media-pct mt-1 text-xs text-slate-500">0%</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif (($block['type'] ?? 'text') === 'icon')
                                    <div class="icon-picker" data-icon-picker>
                                        <input type="hidden" name="blocks[{{ $blockKey }}]" value="{{ $blockValue }}">
                                        <button type="button" class="icon-picker-trigger">
                                            <i data-lucide="{{ $blockValue ?: 'circle' }}" class="icon-picker-preview h-4 w-4"></i>
                                            <span class="icon-picker-name">{{ $blockValue ?: 'Kies een pictogram' }}</span>
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

