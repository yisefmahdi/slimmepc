@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    {{-- Google Fonts Inter + Exact Design System from products.html --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        .webshop-root, .webshop-root button, .webshop-root input, .webshop-root select, .webshop-root textarea {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
        }

        /* =============================
           REVEAL
        ============================= */
        .webshop-root .reveal {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .65s ease, transform .65s cubic-bezier(.2,.75,.2,1);
        }
        .webshop-root .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* =============================
           CATEGORY CHIPS
        ============================= */
        .category-chip {
            transition: background .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
        }
        .category-chip:hover {
            transform: translateY(-1px);
            border-color: #bfdbfe;
        }
        .category-chip.active {
            background: #075ee8 !important;
            border-color: #075ee8 !important;
            color: white !important;
        }

        /* =============================
           PRODUCT CARDS
        ============================= */
        .product-card {
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            border-color: #bfdbfe;
            box-shadow: 0 18px 42px rgba(15,23,42,.08);
        }
        .product-image {
            transition: transform .35s cubic-bezier(.2,.75,.2,1);
        }
        .product-card:hover .product-image {
            transform: scale(1.04);
        }

        /* =============================
           BUTTONS
        ============================= */
        .cart-btn {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .cart-btn:hover {
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 10px 20px rgba(37,99,235,.2);
        }
        .heart-btn {
            transition: transform .2s ease, color .2s ease;
        }
        .heart-btn:hover {
            transform: scale(1.12);
            color: #2563eb;
        }
        .heart-btn.active {
            color: #2563eb;
        }

        /* =============================
           RANGE
        ============================= */
        input[type="range"] {
            accent-color: #2563eb;
        }

        /* =============================
           SIDEBAR
        ============================= */
        .filter-group + .filter-group {
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            margin-top: 20px;
        }

        /* =============================
           WEBSHOP CARD OVERRIDES
           (landing.css has .product-footer flex row + product-body flex:1 — override here)
        ============================= */
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
        /* KEY FIX: product-footer must be BLOCK (not flex-row) and use auto margin to push to bottom */
        .webshop-root .product-footer {
            display: block !important;
            margin-top: auto !important;
            padding-top: 1rem !important;
        }

        /* =============================
           LIST VIEW
        ============================= */
        #productsGrid.list-view {
            grid-template-columns: 1fr !important;
        }
        /* Card: switch to horizontal flex row */
        #productsGrid.list-view .product-card {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 20px !important;
            padding: 12px 16px !important;
            position: relative !important;
        }
        /* Badge/heart row — sit absolute over the image */
        #productsGrid.list-view .product-card > div:first-child {
            position: absolute !important;
            top: 12px !important;
            left: 12px !important;
            width: 156px !important;
            min-height: unset !important;
            z-index: 10 !important;
        }
        /* Image area */
        #productsGrid.list-view .product-media {
            flex-shrink: 0 !important;
            width: 160px !important;
            height: 120px !important;
        }
        /* Content area */
        #productsGrid.list-view .product-body {
            flex: 1 !important;
            min-width: 0 !important;
        }
        #productsGrid.list-view .product-body h3 {
            font-size: 14px !important;
            line-height: 1.4rem !important;
        }
        #productsGrid.list-view .product-body p {
            font-size: 11px !important;
        }
        /* Footer: price + stars on right */
        #productsGrid.list-view .product-footer {
            flex-shrink: 0 !important;
            width: 170px !important;
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        /* =============================
           MOBILE FILTER DRAWER
        ============================= */
        .drawer-backdrop {
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease;
        }
        .drawer-backdrop.open {
            opacity: 1;
            visibility: visible;
        }
        .filter-drawer {
            transform: translateX(-100%);
            transition: transform .3s cubic-bezier(.2,.75,.2,1);
        }
        .filter-drawer.open {
            transform: translateX(0);
        }

        /* =============================
           PRECISE SHADOWS & UTILITIES
        ============================= */
        .drop-shadow-hero {
            filter: drop-shadow(0 20px 25px rgba(15,23,42,.12));
        }
        .shadow-trust-card {
            box-shadow: 0 8px 28px rgba(15,23,42,.04);
        }
        .shadow-filter-box {
            box-shadow: 0 8px 26px rgba(15,23,42,.035);
        }
        .shadow-advice-btn {
            box-shadow: 0 10px 28px rgba(37,99,235,.18);
        }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        @media (max-width: 767px) {
            #productsGrid.list-view .product-card {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            #productsGrid.list-view .product-card > div:first-child {
                position: static !important;
                width: auto !important;
            }
            #productsGrid.list-view .product-media {
                width: 100% !important;
                height: 160px !important;
            }
            #productsGrid.list-view .product-footer {
                width: auto !important;
                margin-top: auto !important;
                padding-top: 1rem !important;
            }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation: none !important;
                transition: none !important;
                scroll-behavior: auto !important;
            }
            .webshop-root .reveal {
                opacity: 1 !important;
                transform: none !important;
            }
        }
    </style>

    <main class="webshop-root bg-[#f8fafc] text-slate-900">

        <!-- =====================================================
             TOP CATEGORY HERO
        ====================================================== -->
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">

                <!-- Breadcrumb -->
                <div class="pt-7 text-[12px] text-slate-500 flex items-center gap-2">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition">
                        Home
                    </a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ route('webshop.category', $currentCategory->slug) }}" class="hover:text-blue-600 transition">
                        Webshop
                    </a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-slate-700">
                        {{ $currentCategory->name }}
                    </span>
                </div>

                <div class="grid lg:grid-cols-[34%_42%_24%] items-center gap-5 min-h-[250px] py-7">

                    <!-- LEFT -->
                    <div class="reveal">
                        <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.08em] text-blue-600">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            {{ $currentCategory->name }}
                        </span>

                        <h1 class="mt-2 text-[38px] sm:text-[44px] font-black tracking-[-.04em] leading-none text-[#0b1734]">
                            {{ $currentCategory->name }}
                        </h1>

                        <p class="mt-4 max-w-[360px] text-[14px] sm:text-[15px] leading-6 text-slate-500">
                            {{ $currentCategory->description ?: 'Vind de laptop die bij jou past. Kwaliteit, snelheid en betrouwbaarheid.' }}
                        </p>
                    </div>

                    <!-- CENTER IMAGE -->
                    <div class="reveal relative hidden min-h-[210px] lg:flex items-end justify-center">
                        <div class="absolute w-[360px] h-[190px] rounded-full bg-blue-50 blur-sm"></div>

                        @php
                            $catImg = $currentCategory->image;
                            if ($catImg) {
                                $heroImgSrc = str_starts_with($catImg, 'http') ? $catImg : asset('storage/' . $catImg);
                            } else {
                                $heroImgSrc = 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=90';
                            }
                        @endphp

                        <img
                            src="{{ $heroImgSrc }}"
                            alt="{{ $currentCategory->name }}"
                            class="relative z-10 max-h-[205px] w-auto rounded-xl object-contain drop-shadow-hero"
                        >
                    </div>

                    <!-- TRUST CARD -->
                    <div class="reveal">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-trust-card">
                            <div class="flex gap-3">
                                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-[13px] font-extrabold">
                                        Getest door Slimme-PC
                                    </h3>
                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        Alle systemen zijn door ons getest en gecontroleerd.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>


        <!-- =====================================================
             QUICK CATEGORY CHIPS (FROM DATABASE)
        ====================================================== -->
        <section class="bg-white">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="flex gap-3 overflow-x-auto pb-5 scrollbar-hide">
                    @foreach($allCategories as $cat)
                        @php
                            $isActive = $currentCategory->id === $cat->id;
                            $iconName = $cat->icon ?: ($cat->slug === 'labtop' || $cat->slug === 'laptops' ? 'laptop' : ($cat->slug === 'gaming-pc' ? 'gamepad-2' : ($cat->slug === 'onderdelen' ? 'cpu' : 'folder')));
                        @endphp
                        <a
                            href="{{ route('webshop.category', $cat->slug) }}"
                            class="category-chip whitespace-nowrap flex items-center gap-2 rounded-xl border px-4 py-2.5 text-[12px] font-semibold transition {{ $isActive ? 'active bg-blue-600 border-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-blue-200 hover:text-blue-600' }}"
                        >
                            <i data-lucide="{{ $iconName }}" class="w-4 h-4"></i>
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- =====================================================
             MAIN SHOP AREA
        ====================================================== -->
        <section class="py-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid lg:grid-cols-[250px_1fr] xl:grid-cols-[265px_1fr] gap-6">

                    <!-- =================================================
                         DESKTOP FILTERS
                    ================================================== -->
                    <aside class="hidden lg:block">
                        <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-filter-box">

                            <div class="flex items-center justify-between">
                                <h2 class="font-extrabold text-[15px]">
                                    Filters
                                </h2>
                                <a
                                    href="{{ route('webshop.category', $currentCategory->slug) }}"
                                    class="flex items-center gap-1 text-[11px] font-semibold text-blue-600 hover:text-blue-700"
                                >
                                    Wis alles
                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                </a>
                            </div>

                            <!-- BRAND -->
                            @if($availableBrands->isNotEmpty())
                                <div class="filter-group">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-[12px] font-bold">
                                            Merk
                                        </h3>
                                        <i data-lucide="chevron-up" class="w-4 h-4 text-slate-400"></i>
                                    </div>

                                    <div class="mt-4 space-y-2.5">
                                        @foreach($availableBrands as $b)
                                            @php $checked = in_array($b['name'], explode(',', request('brand',''))); @endphp
                                            <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    class="filter-checkbox rounded text-blue-600 focus:ring-blue-500 brand-filter"
                                                    value="{{ $b['name'] }}"
                                                    {{ $checked ? 'checked' : '' }}
                                                >
                                                {{ $b['name'] }}
                                                <span class="text-slate-400">({{ $b['count'] }})</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- PRICE -->
                            <div class="filter-group">
                                <h3 class="text-[12px] font-bold">
                                    Prijs
                                </h3>

                                <div class="mt-4">
                                    <input
                                        id="priceRange"
                                        type="range"
                                        min="0"
                                        max="2000"
                                        value="{{ request('price', request('price_max', 2000)) }}"
                                        class="w-full"
                                    >

                                    <div class="mt-1 flex justify-between text-[10px] text-slate-500">
                                        <span>€0</span>
                                        <span>€2000+</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 mt-4">
                                    <button
                                        type="button"
                                        data-price-min="0"
                                        data-price-max="500"
                                        class="price-preset-btn rounded-lg bg-slate-50 border border-slate-200 px-2 py-2 text-[10px] hover:bg-blue-50 hover:border-blue-200 transition"
                                    >
                                        €0 - €500
                                    </button>

                                    <button
                                        type="button"
                                        data-price-min="500"
                                        data-price-max="1000"
                                        class="price-preset-btn rounded-lg bg-slate-50 border border-slate-200 px-2 py-2 text-[10px] hover:bg-blue-50 hover:border-blue-200 transition"
                                    >
                                        €500 - €1000
                                    </button>

                                    <button
                                        type="button"
                                        data-price-min="1000"
                                        data-price-max="1500"
                                        class="price-preset-btn rounded-lg bg-slate-50 border border-slate-200 px-2 py-2 text-[10px] hover:bg-blue-50 hover:border-blue-200 transition"
                                    >
                                        €1000 - €1500
                                    </button>

                                    <button
                                        type="button"
                                        data-price-min="1500"
                                        data-price-max="2000"
                                        class="price-preset-btn rounded-lg bg-slate-50 border border-slate-200 px-2 py-2 text-[10px] hover:bg-blue-50 hover:border-blue-200 transition"
                                    >
                                        €1500+
                                    </button>
                                </div>
                            </div>

                            @if(!empty($filterGroups))
                                @foreach($filterGroups as $group)
                                    @php
                                        $isExtraGroup = $loop->index >= 2; // first 4 filters = Merk + Prijs + first 2 dynamic groups
                                        $groupKey = $group['title'];
                                        $groupSlug = $group['slug'];
                                        $selectedVals = array_filter(array_map('trim', explode(',', request()->query($groupKey, ''))));
                                        // Also try lowercased key fallback (e.g. processor vs Processor)
                                        if(empty($selectedVals)){
                                            $selectedVals = array_filter(array_map('trim', explode(',', request()->query($groupSlug, ''))));
                                        }
                                        $hasMore = count($group['values']) > 10;
                                    @endphp
                                    <div class="filter-group {{ $isExtraGroup ? 'hidden extra-filter-group' : '' }}" data-filter-group="{{ $groupKey }}">
                                        <div class="flex items-center justify-between cursor-pointer filter-group-toggle">
                                            <h3 class="text-[12px] font-bold">{{ $group['title'] }}</h3>
                                            <i data-lucide="chevron-up" class="w-4 h-4 text-slate-400 transition-transform"></i>
                                        </div>
                                        <div class="mt-4 space-y-2.5 filter-group-values">
                                            @foreach($group['values'] as $idx => $v)
                                                @php $checked = in_array($v['raw'], $selectedVals) || in_array($v['display'], $selectedVals); @endphp
                                                <label class="flex items-center gap-2 text-[12px] text-slate-600 cursor-pointer {{ $idx >= 10 ? 'hidden extra-value' : '' }}">
                                                    <input
                                                        type="checkbox"
                                                        class="filter-checkbox rounded text-blue-600 focus:ring-blue-500 dynamic-filter"
                                                        data-title="{{ $groupKey }}"
                                                        value="{{ $v['raw'] }}"
                                                        {{ $checked ? 'checked' : '' }}
                                                    >
                                                    {{ $v['display'] }}
                                                    <span class="text-slate-400">({{ $v['count'] }})</span>
                                                </label>
                                            @endforeach
                                            @if($hasMore)
                                                <button type="button" class="toggle-more mt-1 text-[11px] font-semibold text-blue-600 hover:text-blue-700" data-target="{{ $groupKey }}">
                                                    Toon meer ({{ count($group['values']) - 10 }})
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(!empty($filterGroups) && count($filterGroups) > 2)
                                <button type="button" id="toggleMoreFilters" class="mt-5 w-full rounded-xl border border-blue-200 bg-white py-3 text-[11px] font-semibold text-blue-600 hover:bg-blue-50 transition">
                                    Toon meer filters
                                </button>
                            @endif

                        </div>
                    </aside>


                    <!-- =================================================
                         PRODUCTS SIDE
                    ================================================== -->
                    <div>

                        <!-- CONTROL BAR -->
                        <div class="reveal mb-4 flex flex-col gap-3 rounded-2xl bg-white border border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    onclick="openFilters()"
                                    class="lg:hidden flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-[12px] font-semibold"
                                >
                                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                                    Filters
                                </button>

                                <span id="resultCount" class="text-[12px] font-semibold text-slate-700">
                                    {{ $products->total() }} producten gevonden
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="hidden sm:block text-[11px] text-slate-500">
                                    Sorteren op:
                                </span>

                                <select
                                    id="sortSelect"
                                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] outline-none focus:border-blue-300"
                                >
                                    <option value="populair" {{ $sort === 'populair' ? 'selected' : '' }}>Populair</option>
                                    <option value="prijs_asc" {{ $sort === 'prijs_asc' ? 'selected' : '' }}>Prijs laag - hoog</option>
                                    <option value="prijs_desc" {{ $sort === 'prijs_desc' ? 'selected' : '' }}>Prijs hoog - laag</option>
                                    <option value="nieuwste" {{ $sort === 'nieuwste' ? 'selected' : '' }}>Nieuwste</option>
                                </select>

                                <button
                                    id="gridButton"
                                    type="button"
                                    onclick="setGridView()"
                                    class="flex w-9 h-9 items-center justify-center rounded-lg bg-blue-600 text-white"
                                >
                                    <i data-lucide="grid-2x2" class="w-4 h-4"></i>
                                </button>

                                <button
                                    id="listButton"
                                    type="button"
                                    onclick="setListView()"
                                    class="hidden sm:flex w-9 h-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500"
                                >
                                    <i data-lucide="list" class="w-4 h-4"></i>
                                </button>
                            </div>

                        </div>


                        <!-- PRODUCT GRID -->
                        <div
                            id="productsGrid"
                            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4"
                        >
                            @forelse($products as $product)
                                @php
                                    $hasDiscount = ($product->old_price && (float)$product->old_price > (float)$product->price) || ($product->discount_value && $product->discounted_price < (float)$product->price);
                                    $finalPrice = $hasDiscount && $product->discount_value ? $product->discounted_price : (float)$product->price;
                                    $oldPrice = (float)($product->old_price ?: $product->price);

                                    // Badge logic matching products.html
                                    $badge = null;
                                    $badgeClass = 'bg-blue-500';
                                    if ($hasDiscount) {
                                        $badge = 'Aanbieding';
                                        $badgeClass = 'bg-red-500';
                                    } elseif ($product->is_featured) {
                                        $badge = 'Populair';
                                        $badgeClass = 'bg-emerald-500';
                                    } elseif (str_contains(strtolower($product->title), 'probook') || str_contains(strtolower($product->title), '15s')) {
                                        $badge = 'Bestseller';
                                        $badgeClass = 'bg-green-500';
                                    } elseif (str_contains(strtolower($product->title), 'vivobook')) {
                                        $badge = 'Nieuw';
                                        $badgeClass = 'bg-blue-500';
                                    }

                                    // Specs formatting - supports both legacy string[] and new [{title,value}]
                                    $specs = '';
                                    if (!empty($product->features) && is_array($product->features)) {
                                        $featStrs = array_map(function($f){
                                            if(is_array($f) && isset($f['value'])){ $t=trim($f['title']??''); $v=trim($f['value']); return $t!=='' ? $t.': '.$v : $v; }
                                            return (string)$f;
                                        }, $product->features);
                                        $featStrs = array_values(array_filter($featStrs));
                                        if(!empty($featStrs)) $specs = implode(' · ', $featStrs);
                                    }
                                    if ($specs === '' && !empty($product->summary)) {
                                        $specs = $product->summary;
                                    } elseif ($product->brand) {
                                        $specs = $product->brand . ($product->category ? ' · ' . $product->category->name : '');
                                    } else {
                                        $specs = '';
                                    }

                                    // Image helper - use local placeholder if no image
                                    $galleryFirst = !empty($product->gallery_images) && is_array($product->gallery_images) ? ($product->gallery_images[0] ?? null) : null;
                                    $pImg = $product->main_image ?: $galleryFirst;
                                    $placeholderSrc = asset('assets/img/product-placeholder.jpg');
                                    if ($pImg) {
                                        if (str_starts_with($pImg, 'http')) {
                                            $imgSrc = $pImg;
                                        } elseif (str_starts_with($pImg, 'assets/')) {
                                            $imgSrc = asset($pImg);
                                        } else {
                                            $imgSrc = asset('storage/' . $pImg);
                                        }
                                    } else {
                                        $imgSrc = $placeholderSrc;
                                    }

                                    // Rating — dynamisch uit DB (avg + count), alleen goedgekeurde reviews
                                    $reviewCount = (int) ($product->rating_count ?? 0);
                                    $avgRating = (float) ($product->rating_avg ?? 0);
                                @endphp

                                <article
                                    class="product-card reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-4"
                                    data-price="{{ $finalPrice }}"
                                    data-brand="{{ strtolower($product->brand ?? '') }}"
                                    data-title="{{ strtolower($product->title) }}"
                                >
                                    @if($badge)
                                        <div class="flex items-start justify-between min-h-[28px]">
                                            <span class="rounded-full {{ $badgeClass }} px-2.5 py-1 text-[9px] font-bold text-white">
                                                {{ $badge }}
                                            </span>

                                            <button class="heart-btn text-slate-400" onclick="toggleHeart(this)" type="button">
                                                <i data-lucide="heart" class="w-[18px] h-[18px]"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="flex justify-end min-h-[28px]">
                                            <button class="heart-btn text-slate-400" onclick="toggleHeart(this)" type="button">
                                                <i data-lucide="heart" class="w-[18px] h-[18px]"></i>
                                            </button>
                                        </div>
                                    @endif

                                    <a href="{{ route('webshop.product', [$product->category->slug, $product->slug]) }}" class="product-media flex h-[180px] items-center justify-center">
                                        <img
                                            src="{{ $imgSrc }}"
                                            class="product-image max-h-[155px] max-w-full rounded-xl object-contain"
                                            alt="{{ $product->title }}"
                                            onerror="this.src='{{ $placeholderSrc }}'"
                                        >
                                    </a>

                                    <div class="product-body">
                                        <a href="{{ route('webshop.product', [$product->category->slug, $product->slug]) }}">
                                            <h3 class="text-[12px] font-extrabold leading-5 hover:text-blue-600 transition">
                                                {{ $product->title }}
                                            </h3>
                                        </a>

                                        <p class="mt-1 text-[10px] text-slate-500">
                                            {{ $specs }}
                                        </p>
                                    </div>

                                    <div class="product-footer mt-auto pt-4">
                                        @if($hasDiscount)
                                            <div class="flex items-center gap-2">
                                                <span class="text-[16px] font-black">
                                                    €{{ number_format($finalPrice, 2, ',', '.') }}
                                                </span>
                                                <span class="text-[10px] text-slate-400 line-through">
                                                    €{{ number_format($oldPrice, 2, ',', '.') }}
                                                </span>
                                            </div>
                                        @else
                                            <div class="text-[16px] font-black">
                                                €{{ number_format($finalPrice, 2, ',', '.') }}
                                            </div>
                                        @endif

                                        <div class="mt-3 flex items-center justify-between">
                                            <div class="text-[11px] flex items-center gap-1.5">
                                                <span class="text-amber-400">
                                                    @php $r = $avgRating; @endphp
                                                    @for($i=1;$i<=5;$i++)
                                                        @if($r >= $i) ★ @elseif($r >= $i-0.5) ⯪ @else <span class="text-slate-300">★</span> @endif
                                                    @endfor
                                                </span>
                                                <span class="font-bold {{ $reviewCount>0 ? 'text-slate-700' : 'text-slate-400' }}">{{ $reviewCount>0 ? number_format($avgRating,1) : '—' }}</span>
                                                <span class="text-slate-400">({{ $reviewCount }})</span>
                                            </div>

                                            <x-add-to-cart :product="$product" variant="grid" />
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <i data-lucide="package-open" class="w-6 h-6"></i>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-700">Geen producten gevonden</p>
                                    <p class="mt-1 text-xs text-slate-500">Probeer een andere categorie of filter.</p>
                                    <a href="{{ route('home') }}" class="mt-4 inline-flex rounded-xl bg-blue-600 px-5 py-2.5 text-xs font-bold text-white">
                                        Terug naar home
                                    </a>
                                </div>
                            @endforelse
                        </div>


                        <!-- PAGINATION -->
                        @if($products->hasPages())
                            <div class="reveal mt-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="text-[11px] text-slate-500">
                                    Toont {{ $products->firstItem() }}–{{ $products->lastItem() }} van de {{ $products->total() }} resultaten
                                </div>

                                <div class="flex items-center gap-2">
                                    {{ $products->onEachSide(1)->links('vendor.pagination.webshop') }}
                                </div>

                                <select onchange="location.href=updateQuery('per_page', this.value)" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px]">
                                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 per pagina</option>
                                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 per pagina</option>
                                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 per pagina</option>
                                </select>
                            </div>
                        @else
                            <div class="reveal mt-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="text-[11px] text-slate-500">
                                    Toont {{ $products->firstItem() ?? 1 }}–{{ $products->lastItem() ?? $products->count() }} van de {{ $products->total() }} resultaten
                                </div>

                                <div class="flex items-center gap-2">
                                    <button class="w-9 h-9 rounded-lg bg-blue-600 text-white text-[12px] font-bold">1</button>
                                </div>

                                <select onchange="location.href=updateQuery('per_page', this.value)" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px]">
                                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12 per pagina</option>
                                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24 per pagina</option>
                                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48 per pagina</option>
                                </select>
                            </div>
                        @endif

                    </div>

                </div>
            </div>
        </section>


        <!-- =====================================================
             ADVICE CTA
        ====================================================== -->
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

                        <a
                            href="{{ route('contact') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-[12px] font-bold text-white shadow-advice-btn hover:bg-blue-700 transition"
                        >
                            Persoonlijk advies aanvragen
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- =====================================================
             TRUST BAR
        ====================================================== -->
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


    <!-- =========================================================
         MOBILE FILTER DRAWER
    ========================================================= -->
    <div
        id="drawerBackdrop"
        onclick="closeFilters()"
        class="drawer-backdrop fixed inset-0 z-[80] bg-slate-950/40 backdrop-blur-[2px] lg:hidden"
    ></div>

    <aside
        id="mobileFilterDrawer"
        class="filter-drawer fixed left-0 top-0 bottom-0 z-[90] w-[88%] max-w-[360px] overflow-y-auto bg-white p-5 shadow-2xl lg:hidden"
    >
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-extrabold">Filters</h2>
            <button
                onclick="closeFilters()"
                type="button"
                class="flex w-9 h-9 items-center justify-center rounded-lg bg-slate-100"
            >
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        @if($availableBrands->isNotEmpty())
            <div class="filter-group">
                <h3 class="text-[12px] font-bold">Merk</h3>
                <div class="mt-4 space-y-3">
                    @foreach($availableBrands as $b)
                        @php $checked = in_array($b['name'], explode(',', request('brand',''))); @endphp
                        <label class="flex items-center gap-2 text-[12px] cursor-pointer">
                            <input
                                type="checkbox"
                                class="filter-checkbox brand-filter rounded text-blue-600"
                                value="{{ $b['name'] }}"
                                {{ $checked ? 'checked' : '' }}
                            >
                            {{ $b['name'] }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="filter-group">
            <h3 class="text-[12px] font-bold">Prijs</h3>
            <input
                type="range"
                min="0"
                max="2000"
                value="{{ request('price', 2000) }}"
                class="w-full mt-4"
                id="mobilePriceRange"
            >
        </div>

        @if(!empty($filterGroups))
            @foreach($filterGroups as $group)
                @php
                    $isExtraGroupM = $loop->index >= 2;
                    $groupKey = $group['title'];
                    $selectedValsM = array_filter(array_map('trim', explode(',', request()->query($groupKey, request()->query(strtolower($groupKey), '')))));
                    $hasMoreM = count($group['values']) > 10;
                @endphp
                <div class="filter-group {{ $isExtraGroupM ? 'hidden extra-filter-group-m' : '' }}">
                    <h3 class="text-[12px] font-bold">{{ $group['title'] }}</h3>
                    <div class="mt-4 space-y-3">
                        @foreach($group['values'] as $idx => $v)
                            @php $checkedM = in_array($v['raw'], $selectedValsM) || in_array($v['display'], $selectedValsM); @endphp
                            <label class="flex items-center gap-2 text-[12px] cursor-pointer {{ $idx >= 10 ? 'hidden extra-value-m' : '' }}">
                                <input
                                    type="checkbox"
                                    class="filter-checkbox dynamic-filter-mobile rounded text-blue-600"
                                    data-title="{{ $groupKey }}"
                                    value="{{ $v['raw'] }}"
                                    {{ $checkedM ? 'checked' : '' }}
                                >
                                {{ $v['display'] }} <span class="text-slate-400">({{ $v['count'] }})</span>
                            </label>
                        @endforeach
                        @if($hasMoreM)
                            <button type="button" class="toggle-more-m text-[11px] font-semibold text-blue-600" data-target-m="{{ $groupKey }}">Toon meer ({{ count($group['values']) - 10 }})</button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        @if(!empty($filterGroups) && count($filterGroups) > 2)
            <button type="button" id="toggleMoreFiltersMobile" class="mt-5 w-full rounded-xl border border-blue-200 bg-white py-3 text-[11px] font-semibold text-blue-600 hover:bg-blue-50 transition lg:hidden">
                Toon meer filters
            </button>
        @endif

        <div class="sticky bottom-0 mt-7 bg-white pt-4">
            <button
                onclick="applyFilters(); closeFilters();"
                type="button"
                class="w-full rounded-xl bg-blue-600 py-3 text-[13px] font-bold text-white"
            >
                Bekijk {{ $products->total() }} producten
            </button>
        </div>
    </aside>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }

            /* =========================================
               REVEAL ANIMATIONS
            ========================================= */
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

            // Ensure reveal items are shown if already in viewport
            setTimeout(() => {
                revealItems.forEach(el => {
                    const rect = el.getBoundingClientRect();
                    if (rect.top < window.innerHeight) {
                        el.classList.add('show');
                    }
                });
            }, 100);
        });

        /* =========================================
           HEART TOGGLE
        ========================================= */
        function toggleHeart(button) {
            button.classList.toggle('active');
            const icon = button.querySelector('svg');
            if (!icon) return;
            if (button.classList.contains('active')) {
                icon.setAttribute('fill', 'currentColor');
            } else {
                icon.setAttribute('fill', 'none');
            }
        }



        /* =========================================
           GRID / LIST VIEW SWITCHER
        ========================================= */
        const productsGrid = document.getElementById('productsGrid');
        const gridButton = document.getElementById('gridButton');
        const listButton = document.getElementById('listButton');

        function setGridView() {
            if (!productsGrid) return;
            productsGrid.classList.remove('list-view');
            gridButton.classList.add('bg-blue-600', 'text-white');
            gridButton.classList.remove('bg-slate-100', 'text-slate-500');
            listButton.classList.remove('bg-blue-600', 'text-white');
            listButton.classList.add('bg-slate-100', 'text-slate-500');
        }

        function setListView() {
            if (!productsGrid) return;
            productsGrid.classList.add('list-view');
            listButton.classList.add('bg-blue-600', 'text-white');
            listButton.classList.remove('bg-slate-100', 'text-slate-500');
            gridButton.classList.remove('bg-blue-600', 'text-white');
            gridButton.classList.add('bg-slate-100', 'text-slate-500');
        }

        /* =========================================
           MOBILE FILTER DRAWER
        ========================================= */
        const filterDrawer = document.getElementById('mobileFilterDrawer');
        const drawerBackdrop = document.getElementById('drawerBackdrop');

        function openFilters() {
            if (!filterDrawer || !drawerBackdrop) return;
            filterDrawer.classList.add('open');
            drawerBackdrop.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeFilters() {
            if (!filterDrawer || !drawerBackdrop) return;
            filterDrawer.classList.remove('open');
            drawerBackdrop.classList.remove('open');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeFilters();
        });

        /* =========================================
           URL & FILTERS DISPATCH
        ========================================= */
        function updateQuery(key, value) {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
            url.searchParams.delete('page');
            return url.toString();
        }

        function collectDynamicFilters() {
            const map = {};
            document.querySelectorAll('.dynamic-filter:checked, .dynamic-filter-mobile:checked').forEach(cb => {
                const title = cb.getAttribute('data-title');
                if (!title) return;
                if (!map[title]) map[title] = new Set();
                map[title].add(cb.value);
            });
            return map;
        }

        function applyFilters() {
            const brands = Array.from(document.querySelectorAll('.brand-filter:checked'))
                .map(i => i.value)
                .filter(Boolean);

            const mobilePrice = document.getElementById('mobilePriceRange')?.value;
            const desktopPrice = document.getElementById('priceRange')?.value;
            const price = mobilePrice || desktopPrice;

            const url = new URL(window.location.href);
            url.searchParams.delete('page');

            if (brands.length > 0) {
                url.searchParams.set('brand', brands.join(','));
            } else {
                url.searchParams.delete('brand');
            }

            // Dynamic features: grouped by title, e.g. ?Processor=Intel%20Core%20i5,Intel%20Core%20i7&RAM=16GB
            const dynMap = collectDynamicFilters();
            // Remove old dynamic params that are not in current selection (clean up)
            document.querySelectorAll('.dynamic-filter, .dynamic-filter-mobile').forEach(cb => {
                const t = cb.getAttribute('data-title');
                if (t) url.searchParams.delete(t);
                url.searchParams.delete(t.toLowerCase());
            });
            Object.entries(dynMap).forEach(([title, set]) => {
                const vals = Array.from(set);
                if (vals.length > 0) {
                    url.searchParams.set(title, vals.join(','));
                }
            });

            if (price && Number(price) < 2000) {
                url.searchParams.set('price', price);
            } else {
                url.searchParams.delete('price');
            }

            window.location.href = url.toString();
        }

        document.querySelectorAll('.brand-filter').forEach(cb => {
            cb.addEventListener('change', applyFilters);
        });
        document.querySelectorAll('.dynamic-filter, .dynamic-filter-mobile').forEach(cb => {
            cb.addEventListener('change', applyFilters);
        });
        // Toon meer / Toon minder per group (desktop)
        document.querySelectorAll('.toggle-more').forEach(btn => {
            btn.addEventListener('click', () => {
                const group = btn.getAttribute('data-target');
                const container = btn.closest('.filter-group');
                if (!container) return;
                const hidden = container.querySelectorAll('.extra-value');
                const isHidden = hidden.length > 0 && hidden[0].classList.contains('hidden');
                hidden.forEach(el => el.classList.toggle('hidden', !isHidden));
                btn.textContent = isHidden ? 'Toon minder' : `Toon meer (${hidden.length})`;
            });
        });
        document.querySelectorAll('.toggle-more-m').forEach(btn => {
            btn.addEventListener('click', () => {
                const container = btn.closest('.filter-group');
                if (!container) return;
                const hidden = container.querySelectorAll('.extra-value-m');
                const isHidden = hidden.length > 0 && hidden[0].classList.contains('hidden');
                hidden.forEach(el => el.classList.toggle('hidden', !isHidden));
                btn.textContent = isHidden ? 'Toon minder' : `Toon meer (${hidden.length})`;
            });
        });
        // Global Toon meer filters (first 4 visible) — desktop & mobile
        const moreBtn = document.getElementById('toggleMoreFilters');
        if (moreBtn) {
            moreBtn.addEventListener('click', () => {
                const hiddenGroups = document.querySelectorAll('.extra-filter-group');
                const isHidden = hiddenGroups.length > 0 && hiddenGroups[0].classList.contains('hidden');
                hiddenGroups.forEach(el => el.classList.toggle('hidden', !isHidden));
                moreBtn.textContent = isHidden ? 'Toon minder filters' : 'Toon meer filters';
            });
        }
        const moreBtnM = document.getElementById('toggleMoreFiltersMobile');
        if (moreBtnM) {
            moreBtnM.addEventListener('click', () => {
                const hiddenGroups = document.querySelectorAll('.extra-filter-group-m');
                const isHidden = hiddenGroups.length > 0 && hiddenGroups[0].classList.contains('hidden');
                hiddenGroups.forEach(el => el.classList.toggle('hidden', !isHidden));
                moreBtnM.textContent = isHidden ? 'Toon minder filters' : 'Toon meer filters';
            });
        }

        document.querySelectorAll('.price-preset-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const min = btn.dataset.priceMin;
                const max = btn.dataset.priceMax;
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('price');
                if (min) url.searchParams.set('price_min', min);
                if (max) url.searchParams.set('price_max', max);
                window.location.href = url.toString();
            });
        });

        const priceRangeInput = document.getElementById('priceRange');
        if (priceRangeInput) {
            priceRangeInput.addEventListener('change', e => {
                const url = new URL(window.location.href);
                url.searchParams.delete('page');
                url.searchParams.delete('price_min');
                url.searchParams.delete('price_max');
                if (Number(e.target.value) < 2000) {
                    url.searchParams.set('price', e.target.value);
                } else {
                    url.searchParams.delete('price');
                }
                window.location.href = url.toString();
            });
        }

        /* =========================================
           SORT SELECTION
        ========================================= */
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                window.location.href = updateQuery('sort', sortSelect.value);
            });
        }
    </script>
@endsection