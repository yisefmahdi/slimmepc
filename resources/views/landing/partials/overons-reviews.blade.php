@php $reviews = $o['reviews'] ?? []; @endphp

<section>
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20">
        <div class="reveal flex items-center justify-between gap-4">
            <div>
                @if (!empty($reviews['badge']))
                <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $reviews['badge'] }}</span>
                @endif
            </div>
            @if (count($reviews['items'] ?? []) > 1)
            <div class="flex shrink-0 items-center gap-2">
                <button id="reviewPrev" type="button" aria-label="Vorige" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </button>
                <button id="reviewNext" type="button" aria-label="Volgende" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </button>
            </div>
            @endif
        </div>

        @if (count($reviews['items'] ?? []))
        <div id="reviewTrack" class="werkplaats-track review-track mt-8">
            @foreach ($reviews['items'] as $item)
            @php $stars = min(max((int) ($item['stars'] ?? 5), 1), 5); @endphp
            <article class="werkplaats-card review-card rounded-[24px] border border-blue-100 bg-white p-6 ">
                <div class="flex items-center justify-between">
                    <div class="flex text-amber-400">
                        @for ($i = 0; $i < $stars; $i++)
                        <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                        @endfor
                    </div>
                    <span class="text-xl font-black text-blue-500">G</span>
                </div>

                @if (!empty($item['quote']))
                <p class="mt-5 text-sm leading-7 text-slate-600">{{ $item['quote'] }}</p>
                @endif

                @if (!empty($item['name']))
                <p class="mt-5 text-sm font-black">— {{ $item['name'] }}</p>
                @endif
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('reviewTrack');
    const prev = document.getElementById('reviewPrev');
    const next = document.getElementById('reviewNext');
    if (!track || !prev || !next) return;
    const scrollAmount = () => {
        const card = track.querySelector('.review-card');
        return card ? card.offsetWidth + 16 : 340;
    };
    prev.addEventListener('click', () => track.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
    if (window.lucide) lucide.createIcons();
});
</script>
