@php $l = $p['locatie'] ?? []; @endphp

<section id="locatie" class="px-4 pb-14 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-sm">
        <div class="grid lg:grid-cols-[0.75fr_1.4fr]">

            <div class="p-8 lg:p-10">
                @if (!empty($l['badge']))
                <p class="text-sm font-bold uppercase tracking-wide text-blue-600">{{ $l['badge'] }}</p>
                @endif

                <h2 class="mt-3 text-3xl font-black leading-tight text-[#0b1f4d]">
                    {{ $l['title_line1'] ?? '' }}
                    @if (!empty($l['title_line2']))
                    <br><span class="text-blue-600">{{ $l['title_line2'] }}</span>
                    @endif
                </h2>

                @if (!empty($l['description']))
                <p class="mt-4 leading-relaxed text-slate-600">{{ $l['description'] }}</p>
                @endif

                @if (count($l['location_items'] ?? []))
                <div class="mt-7 space-y-5 text-sm">
                    @foreach ($l['location_items'] as $item)
                    <div class="flex items-start gap-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i data-lucide="{{ $item['icon'] ?? 'map-pin' }}" class="h-5 w-5"></i>
                        </span>
                        <div>
                            @if (!empty($item['title']))
                            <p class="font-bold text-slate-900">{{ $item['title'] }}</p>
                            @endif
                            <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-600">{{ $item['text'] ?? '' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if (!empty($l['route_label']))
                <a href="{{ $l['route_url'] ?? '#' }}" target="_blank" rel="noopener"
                   class="mt-8 inline-flex items-center gap-3 rounded-xl bg-blue-600 px-6 py-3.5 font-bold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-500">
                    {{ $l['route_label'] }}
                    <span aria-hidden="true">↗</span>
                </a>
                @endif
            </div>

            <div class="min-h-[420px] bg-slate-100">
                @if (!empty($l['map_src']))
                <iframe src="{{ $l['map_src'] }}" width="100%" height="100%" style="border:0; min-height:420px;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        title="Slimme-PC locatie in Apeldoorn"></iframe>
                @endif
            </div>

        </div>
    </div>
</section>