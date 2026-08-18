@php $meet = $o['meet'] ?? []; @endphp

<section class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-14 sm:px-8 lg:grid-cols-[.78fr_1.22fr] lg:px-10 lg:py-20">
    <div class="reveal">
        <div class="overflow-hidden rounded-[28px] border border-blue-100 bg-slate-100 shadow-soft">
            @if (!empty($meet['image']))
            <img src="{{ asset($meet['image']) }}" alt="{{ $meet['image_alt'] ?? 'Mohammed, eigenaar van Slimme-PC' }}"
                 class="aspect-[4/5] w-full object-cover" loading="lazy" decoding="async">
            @endif
        </div>
    </div>

    <div class="reveal">
        @if (!empty($meet['badge']))
        <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $meet['badge'] }}</span>
        @endif

        <h2 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">
            {{ $meet['title_prefix'] ?? '' }} <span class="text-blue-600">{{ $meet['title_highlight'] ?? '' }}</span>
        </h2>

        @if (!empty($meet['description']))
        <p class="mt-5 text-base leading-8 text-slate-600">{{ $meet['description'] }}</p>
        @endif

        @if (count($meet['points'] ?? []))
        <div class="mt-7 space-y-4">
            @foreach ($meet['points'] as $pt)
            <div class="flex gap-3">
                <i data-lucide="{{ $pt['icon'] ?? 'circle-check' }}" class="mt-1 h-5 w-5 shrink-0 text-blue-600"></i>
                <p class="text-sm leading-6 text-slate-700">{{ $pt['label'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-7">
            @if (!empty($meet['sign_name']))
            <p class="text-3xl italic text-slate-900">{{ $meet['sign_name'] }}</p>
            @endif
            @if (!empty($meet['sign_role']))
            <p class="mt-2 text-sm text-slate-500">{{ $meet['sign_role'] }}</p>
            @endif
        </div>
    </div>
</section>