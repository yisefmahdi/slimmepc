@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        .webshop-root, .webshop-root button, .webshop-root input, .webshop-root select, .webshop-root textarea {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        }
        .webshop-root .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .65s ease, transform .65s cubic-bezier(.2,.75,.2,1);
        }
        .webshop-root .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }
        .product-card {
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease, opacity .25s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #bfdbfe;
            box-shadow: 0 18px 42px rgba(15,23,42,.08);
        }
        .product-card.removing {
            opacity: 0;
            transform: translateX(-15px);
        }
        .product-image {
            transition: transform .35s cubic-bezier(.2,.75,.2,1);
        }
        .product-card:hover .product-image {
            transform: scale(1.04);
        }
        .heart-btn {
            transition: transform .2s ease, color .2s ease, border-color .2s ease, background-color .2s ease;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            background: transparent;
            border-radius: 9999px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .heart-btn:hover {
            transform: scale(1.12);
            color: #e11d48;
            border-color: #fecdd3;
            background: #fff1f2;
        }
        .heart-btn.active {
            color: #e11d48 !important;
            border-color: #fecdd3 !important;
            background: #fff1f2 !important;
        }
        .heart-btn svg {
            fill: none !important;
            stroke: currentColor !important;
            transition: fill .2s ease, stroke .2s ease;
        }
        .heart-btn.active svg {
            fill: #e11d48 !important;
            stroke: #e11d48 !important;
        }
        .webshop-root .product-card {
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            background: white !important;
            box-shadow: none !important;
            padding: 1rem !important;
        }
        .webshop-root .product-body {
            display: block !important;
            flex: unset !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .webshop-root .product-body h3 {
            font-size: 12px !important;
            font-weight: 800 !important;
            line-height: 1.25rem !important;
            color: #0f172a !important;
            margin: 0 !important;
        }
        .webshop-root .product-body p {
            margin-top: 4px !important;
            min-height: unset !important;
            font-size: 10px !important;
            line-height: 1rem !important;
            color: #64748b !important;
        }
        .webshop-root .product-image {
            width: auto !important;
            height: auto !important;
            max-height: 155px !important;
            max-width: 100% !important;
            border-radius: 0.75rem !important;
            object-fit: contain !important;
            filter: none !important;
            transition: transform .35s cubic-bezier(.2,.75,.2,1) !important;
        }
        .webshop-root .product-card:hover .product-image {
            transform: scale(1.04) !important;
            filter: none !important;
        }
        .webshop-root .product-footer {
            display: block !important;
            margin-top: auto !important;
            padding-top: 1rem !important;
        }
        .category-chip {
            transition: all .15s ease;
        }
        .category-chip.active {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }
        .pagination-link {
            transition: all .15s ease;
        }
        .pagination-link:hover {
            background-color: #eff6ff;
            border-color: #2563eb;
            color: #2563eb;
        }
        .pagination-link.active {
            background-color: #2563eb;
            border-color: #2563eb;
            color: white;
        }
    </style>

    <main class="webshop-root bg-[#f8fafc] text-slate-900">

        {{-- TOP CATEGORY HERO --}}
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid lg:grid-cols-[1fr_auto] items-center gap-5 min-h-[250px] py-7">
                    <div class="reveal">
                        <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.08em] text-blue-600">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Verlanglijstje
                        </span>
                        <h1 class="mt-2 text-[38px] sm:text-[44px] font-black tracking-[-.04em] leading-none text-[#0b1734]">
                            Je favorieten
                        </h1>
                        <p class="mt-4 max-w-[360px] text-[14px] sm:text-[15px] leading-6 text-slate-500">
                            Bewaard producten die je in gedachten hebt. Bekijk ze hier en voeg ze toe aan je winkelwagen wanneer je klaar bent.
                        </p>
                    </div>
                    <div class="reveal">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-trust-card">
                            <div class="flex gap-3">
                                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <i data-lucide="heart" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-[13px] font-extrabold">
                                        {{ $products->total() }} product{{ $products->total() !== 1 ? 'en' : '' }} bewaard
                                    </h3>
                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        {{ $products->total() > 0 ? 'Je favorieten staan hier voor je klaar.' : 'Voeg je eerste product toe om te beginnen.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- QUICK CATEGORY CHIPS --}}
        <section class="bg-white">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="flex gap-3 overflow-x-auto pb-5 scrollbar-hide">
                    <a href="{{ route('wishlist.index') }}" class="category-chip whitespace-nowrap flex items-center gap-2 rounded-xl border px-4 py-2.5 text-[12px] font-semibold {{ request('category') === 'all' || !request('category') ? 'active bg-blue-600 border-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:text-blue-600' }}">
                        <i data-lucide="heart" class="w-4 h-4"></i>
                        Alles
                    </a>
                    @foreach($categories as $cat)
                        <a
                            href="{{ route('wishlist.index', array_merge(request()->all(), ['category' => $cat->slug])) }}"
                            class="category-chip whitespace-nowrap flex items-center gap-2 rounded-xl border px-4 py-2.5 text-[12px] font-semibold {{ request('category') === $cat->slug ? 'active bg-blue-600 border-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:text-blue-600' }}"
                        >
                            <i data-lucide="{{ $cat->icon ?: 'folder' }}" class="w-4 h-4"></i>
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- MAIN SHOP AREA --}}
        <section class="py-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">

                {{-- CONTROL BAR --}}
                <div class="reveal mb-4 flex flex-col gap-3 rounded-2xl bg-white border border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[12px] font-semibold text-slate-700">
                            {{ $products->total() }} product{{ $products->total() !== 1 ? 'en' : '' }} gevonden
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="hidden sm:block text-[11px] text-slate-500">Sorteren op:</span>
                        <select id="sortSelect" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] outline-none focus:border-blue-300">
                            <option value="nieuwste" {{ request('sort') === 'nieuwste' ? 'selected' : '' }}>Nieuwste</option>
                            <option value="prijs_asc" {{ request('sort') === 'prijs_asc' ? 'selected' : '' }}>Prijs laag - hoog</option>
                            <option value="prijs_desc" {{ request('sort') === 'prijs_desc' ? 'selected' : '' }}>Prijs hoog - laag</option>
                            <option value="populair" {{ request('sort') === 'populair' ? 'selected' : '' }}>Populair</option>
                        </select>
                    </div>
                </div>

                {{-- PRODUCT GRID --}}
                @if($products->isEmpty())
                    <div class="reveal rounded-2xl border-2 border-dashed border-[#DCE4EF] bg-[#F8FAFF] p-8 sm:p-12 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white border border-[#DDE6F4] text-[#0759F5]">
                            <i data-lucide="heart" class="w-8 h-8"></i>
                        </div>
                        <h3 class="mt-4 text-[18px] font-bold text-[#07173A]">Nog geen favorieten</h3>
                        <p class="mt-2 text-[14px] text-[#3E547F]">Tik op het hartje bij een product om het hier te bewaren.</p>
                        <a href="{{ route('home') }}" class="mt-6 inline-flex items-center gap-2 rounded-[6px] bg-[#0759F5] px-6 py-3 text-[14px] font-semibold text-white hover:bg-[#064ED4] transition">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i> Naar de webshop
                        </a>
                    </div>
                @else
                    <div id="wishlistGrid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach($products as $product)
                            @include('landing.partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                        @endforeach
                    </div>

                    {{-- PAGINATION --}}
                    @if($products->hasPages())
                        <div class="reveal mt-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-[11px] text-slate-500">
                                Toont {{ $products->firstItem() }}–{{ $products->lastItem() }} van de {{ $products->total() }} resultaten
                            </div>
                            <div class="flex items-center gap-1">
                                {{ $products->appends(request()->query())->links('vendor.pagination.webshop') }}
                            </div>
                            <select onchange="location.href=updateQuery('per_page', this.value)" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px]">
                                <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 per pagina</option>
                                <option value="24" {{ request('per_page', 24) == 24 ? 'selected' : '' }}>24 per pagina</option>
                            </select>
                        </div>
                    @endif
                @endif

            </div>
        </section>

        {{-- ADVICE CTA --}}
        <section class="pb-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="reveal relative overflow-hidden rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 via-[#f4f8ff] to-[#edf5ff] px-6 py-7 md:px-9">
                    <div class="grid md:grid-cols-[1fr_auto] gap-6 items-center">
                        <div class="flex items-start gap-5">
                            <div class="hidden sm:flex w-16 h-16 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <i data-lucide="headset" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h2 class="text-[24px] sm:text-[28px] font-black tracking-[-.03em] text-[#0b1734]">
                                    Hulp nodig bij het kiezen?
                                </h2>
                                <p class="mt-2 max-w-[620px] text-[13px] leading-6 text-slate-600">
                                    Wij denken graag met je mee. Vertel ons wat je nodig hebt en wij adviseren je de laptop die het beste bij je past.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('contact') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-[12px] font-bold text-white shadow-advice-btn hover:bg-blue-700 transition shrink-0">
                            Persoonlijk advies aanvragen
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- TRUST BAR --}}
        <section class="pb-12">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="reveal grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 rounded-2xl bg-white border border-slate-200 p-5">
                    <div class="flex items-center gap-3">
                        <div class="flex w-10 h-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i data-lucide="truck" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold">Gratis verzending</div>
                            <div class="text-[10px] text-slate-500 mt-1">vanaf €75</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex w-10 h-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold">Afhalen in Apeldoorn</div>
                            <div class="text-[10px] text-slate-500 mt-1">Binnen openingstijden</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex w-10 h-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i data-lucide="shield-check" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold">Garantie</div>
                            <div class="text-[10px] text-slate-500 mt-1">Op onze producten</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex w-10 h-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                            <i data-lucide="lock-keyhole" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <div class="text-[12px] font-bold">Veilig betalen</div>
                            <div class="text-[10px] text-slate-500 mt-1">Betrouwbare betaalmethodes</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();

            const revealItems = document.querySelectorAll('.webshop-root .reveal');
            const revealObserver = new IntersectionObserver(
                entries => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('show');
                            revealObserver.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.05, rootMargin: '0px 0px -10px 0px' }
            );
            revealItems.forEach((element, index) => {
                element.style.transitionDelay = `${Math.min((index % 5) * 40, 160)}ms`;
                revealObserver.observe(element);
            });
            setTimeout(() => {
                revealItems.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight) el.classList.add('show');
                });
            }, 100);
        });

        const WISHLIST_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

         function setHeartVisual(button, active) {
             button.classList.toggle('active', active);
             button.style.color = active ? '#e11d48' : '';
             const icon = button.querySelector('svg');
             if (icon) {
                 icon.setAttribute('fill', active ? 'currentColor' : 'none');
                 icon.style.color = active ? '#e11d48' : '';
             }
         }

        async function toggleWishlist(productId, button) {
            const wasActive = button.classList.contains('active');
            setHeartVisual(button, !wasActive);
            try {
                const res = await fetch('{{ route('wishlist.toggle') }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': WISHLIST_CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ product_id: productId })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Fout');
                const added = data.status === 'added';
                setHeartVisual(button, added);
                document.querySelectorAll('[data-wishlist-count]').forEach(el => el.textContent = data.count ?? 0);
            } catch (e) {
                setHeartVisual(button, wasActive);
            }
        }

        function toggleHeart(button) {
            const card = button.closest('[data-product-id]');
            const id = card ? parseInt(card.dataset.productId, 10) : null;
            if (id) toggleWishlist(id, button);
        }

        function updateQuery(key, value) {
            const url = new URL(window.location.href);
            if (value) { url.searchParams.set(key, value); } else { url.searchParams.delete(key); }
            return url.toString();
        }
    </script>
@endsection