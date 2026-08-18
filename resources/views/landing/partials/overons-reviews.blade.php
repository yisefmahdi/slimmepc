@php $reviews = $o['reviews'] ?? []; @endphp

<section class="bg-slate-50">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-10 lg:py-20">
        <div class="reveal text-center">
            @if (!empty($reviews['badge']))
            <span class="text-sm font-black uppercase tracking-[0.14em] text-blue-600">{{ $reviews['badge'] }}</span>
            @endif
        </div>

        @if (count($reviews['items'] ?? []))
        <div class="werkplaats-track review-track mt-8">
            @foreach ($reviews['items'] as $item)
            @php $stars = min(max((int) ($item['stars'] ?? 5), 1), 5); @endphp
            <article class="werkplaats-card review-card rounded-[24px] border border-blue-100 bg-white p-6 shadow-card">
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