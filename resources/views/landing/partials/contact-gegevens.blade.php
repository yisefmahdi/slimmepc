@php $g = $p['gegevens'] ?? []; @endphp

<section class="px-4 py-14 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">

            {{-- Card 1: Contactgegevens --}}
            <div class="relative rounded-3xl border border-blue-100 bg-white p-7 pt-10 card-soft">
                <div class="absolute -top-7 left-7 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <i data-lucide="{{ $g['card1_icon'] ?? 'map-pin' }}" class="h-7 w-7"></i>
                </div>
                <h2 class="text-2xl font-black text-[#0b1f4d]">{{ $g['card1_title'] ?? 'Contactgegevens' }}</h2>

                <div class="mt-6 space-y-5 text-sm text-slate-700">
                    <div class="flex gap-4">
                        <div class="mt-0.5 text-lg text-blue-600" aria-hidden="true">●</div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $g['company_name'] ?? '' }}</p>
                            <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-600">{{ $g['address'] ?? '' }}</p>
                        </div>
                    </div>

                    <div class="h-px bg-slate-100"></div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">KvK</span>
                            <span class="font-semibold text-slate-900">{{ $g['kvk'] ?? '' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-slate-500">BTW</span>
                            <span class="font-semibold text-slate-900">{{ $g['btw'] ?? '' }}</span>
                        </div>
                    </div>

                    @if (!empty($g['route_label']))
                    <a href="#locatie"
                       class="inline-flex items-center gap-2 font-bold text-blue-600 transition hover:text-blue-500">
                        {{ $g['route_label'] }}
                        <span aria-hidden="true">→</span>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Card 2: Service & support --}}
            <div class="relative rounded-3xl border border-blue-100 bg-white p-7 pt-10 card-soft">
                <div class="absolute -top-7 left-7 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <i data-lucide="{{ $g['card2_icon'] ?? 'headphones' }}" class="h-7 w-7"></i>
                </div>
                <h2 class="text-2xl font-black text-[#0b1f4d]">{{ $g['card2_title'] ?? 'Service & support' }}</h2>

                <div class="mt-6 space-y-4 text-sm">
                    @foreach (($g['contact_methods'] ?? []) as $method)
                    <a href="{{ $method['url'] ?? '#' }}"
                       @if (str_starts_with((string) ($method['url'] ?? ''), 'http')) target="_blank" rel="noopener" @endif
                       class="group flex items-center gap-4 rounded-xl p-2 transition hover:bg-blue-50">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 transition group-hover:bg-blue-600 group-hover:text-white">
                            <i data-lucide="{{ $method['icon'] ?? 'phone' }}" class="h-5 w-5"></i>
                        </span>
                        <div>
                            <p class="text-xs text-slate-500">{{ $method['label'] ?? '' }}</p>
                            <p class="font-bold text-slate-900">{{ $method['value'] ?? '' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Card 3: Openingstijden --}}
            <div class="relative rounded-3xl border border-blue-100 bg-white p-7 pt-10 card-soft md:col-span-2 lg:col-span-1">
                <div class="absolute -top-7 left-7 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20">
                    <i data-lucide="{{ $g['card3_icon'] ?? 'clock' }}" class="h-7 w-7"></i>
                </div>
                <h2 class="text-2xl font-black text-[#0b1f4d]">{{ $g['card3_title'] ?? 'Openingstijden' }}</h2>

                <div class="mt-6 space-y-5 text-sm">
                    @foreach (($g['opening_hours'] ?? []) as $i => $row)
                    @php
                        $isClosed = !empty($row['closed'])
                            || str_contains(mb_strtolower((string) ($row['time'] ?? '')), 'gesloten');
                    @endphp
                    @if ($i > 0)
                    <div class="h-px bg-slate-100"></div>
                    @endif
                    <div class="flex items-start justify-between gap-5">
                        <div>
                            <p class="font-bold text-slate-900">{{ $row['day'] ?? '' }}</p>
                            @if (!empty($row['note']))
                            <p class="mt-1 {{ $isClosed ? 'text-slate-400' : 'text-slate-500' }}">{{ $row['note'] }}</p>
                            @endif
                        </div>
                        <span class="shrink-0 font-bold {{ $isClosed ? 'text-slate-400' : 'text-blue-600' }}">{{ $row['time'] ?? '' }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>