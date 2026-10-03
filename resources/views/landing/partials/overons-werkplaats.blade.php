@php $werkplaats = $o['werkplaats'] ?? []; @endphp

<section class="mx-auto max-w-7xl px-5 pb-14 sm:px-8 lg:px-10 lg:pb-20">
    <div class="reveal flex items-center justify-between gap-4">
        <div>
            @if (!empty($werkplaats['badge']))
            <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $werkplaats['badge'] }}</span>
            @endif
        </div>
        @if (count($werkplaats['items'] ?? []) > 1)
        <div class="flex shrink-0 items-center gap-2">
            <button id="werkplaatsPrev" type="button" aria-label="Vorige" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                <i data-lucide="chevron-left" class="h-5 w-5"></i>
            </button>
            <button id="werkplaatsNext" type="button" aria-label="Volgende" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                <i data-lucide="chevron-right" class="h-5 w-5"></i>
            </button>
        </div>
        @endif
    </div>

    @if (count($werkplaats['items'] ?? []))
    <div id="werkplaatsTrack" class="werkplaats-track mt-6" data-werkplaats-track>
        @foreach ($werkplaats['items'] as $item)
        <article class="werkplaats-card image-card relative overflow-hidden rounded-[22px] bg-slate-900 shadow-card">
            @if (!empty($item['image']))
            <img src="{{ asset('assets/img/landing/' . basename($item['image'] ?? '')) }}" alt="{{ $item['title'] ?? '' }}"
                 class="h-64 w-full object-cover" loading="lazy" decoding="async">
            @endif
            <div class="werkplaats-card-overlay absolute inset-0"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <p class="flex items-center gap-2 text-sm font-black text-white">
                    <i data-lucide="{{ $item['icon'] ?? 'wrench' }}" class="h-4 w-4 text-blue-400"></i>
                    {{ $item['title'] ?? '' }}
                </p>
            </div>
        </article>
        @endforeach
    </div>
    @endif
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('werkplaatsTrack');
    const prev = document.getElementById('werkplaatsPrev');
    const next = document.getElementById('werkplaatsNext');
    if (!track || !prev || !next) return;
    const scrollAmount = () => {
        const card = track.querySelector('.werkplaats-card');
        return card ? card.offsetWidth + 16 : 340;
    };
    prev.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
    if (window.lucide) lucide.createIcons();
});
</script>