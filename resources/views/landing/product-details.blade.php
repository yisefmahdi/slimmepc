@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    @php
        $hasDiscount = ($product->old_price && (float)$product->old_price > (float)$product->price) || ($product->discount_value && $product->discounted_price < (float)$product->price);
        $finalPrice = $hasDiscount && $product->discount_value ? $product->discounted_price : (float) $product->price;
        $oldPriceVal = (float) ($product->old_price ?: $product->price);
        $inStock = $product->stock_status === 'in_stock';
        $deliveryText = $product->delivery_time ?: null;
        $placeholderSrc = asset('assets/img/product-placeholder.jpg');
        $gallery = [];
        if ($product->main_image) $gallery[] = $product->main_image;
        if (!empty($product->gallery_images) && is_array($product->gallery_images)) {
            foreach ($product->gallery_images as $g) {
                if ($g && !in_array($g, $gallery)) $gallery[] = $g;
            }
        }
        if (empty($gallery)) $gallery[] = $placeholderSrc;
        $resolveImg = function($p) use ($placeholderSrc) {
            if (!$p) return $placeholderSrc;
            if (str_starts_with($p, 'http')) return $p;
            if (str_starts_with($p, 'assets/')) return asset($p);
            if (str_starts_with($p, 'storage/')) return asset($p);
            return asset('storage/' . ltrim($p, '/'));
        };
        // Normalize features: supports both legacy string[] and new [{title,value}]
        $rawFeatures = $product->features ?? [];
        $features = [];
        $featuresDisplay = [];
        if (is_array($rawFeatures)) {
            foreach ($rawFeatures as $f) {
                if (is_array($f) && array_key_exists('title', $f) && array_key_exists('value', $f)) {
                    $t = trim((string)($f['title'] ?? '')); $v = trim((string)($f['value'] ?? ''));
                    if ($t !== '' && $v !== '') { $features[] = ['title'=>$t,'value'=>$v]; $featuresDisplay[] = $t . ': ' . $v; }
                    elseif ($v !== '') { $features[] = ['title'=>'','value'=>$v]; $featuresDisplay[] = $v; }
                    elseif ($t !== '') { $features[] = ['title'=>$t,'value'=>'']; $featuresDisplay[] = $t; }
                } elseif (is_string($f) && trim($f) !== '') {
                    $features[] = ['title'=>'','value'=>trim($f)]; $featuresDisplay[] = trim($f);
                } elseif (is_array($f) && isset($f['value'])) {
                    $v = trim((string)$f['value']); if($v!==''){ $features[] = ['title'=>trim($f['title']??''),'value'=>$v]; $featuresDisplay[] = $v; }
                }
            }
        }
        // Fallback to raw display strings for quick/short
        $featuresStrings = $featuresDisplay;
        // 1) Short specs onder titel = eerste 4 Specificaties (zoals in screenshot top blauw)
        $shortSpecs = !empty($featuresStrings) ? implode(' • ', array_slice($featuresStrings, 0, 4)) : null;
        // Highlights (4 kaarten) — uit highlights json
        $rawHighlights = $product->highlights ?? [];
        $highlights = [];
        if (is_array($rawHighlights)) {
            foreach ($rawHighlights as $h) {
                if (is_array($h) && !empty(trim($h['title'] ?? ''))) {
                    $highlights[] = ['icon'=>trim($h['icon'] ?? ''), 'title'=>trim($h['title']), 'subtitle'=>trim($h['subtitle'] ?? '')];
                } elseif (is_string($h) && trim($h) !== '') {
                    $highlights[] = ['icon'=>'', 'title'=>trim($h), 'subtitle'=>''];
                }
            }
        }
        // 2) Quick cards onder Op voorraad = eerste 4 Highlights (zoals in screenshot bottom)
        $quickSpecs = [];
        foreach (array_slice($highlights, 0, 4) as $h) {
            $quickSpecs[] = ['icon' => $h['icon'] ?: 'star', 'title' => $h['title'], 'sub' => $h['subtitle'] ?? ''];
        }
        // Specificaties — dynamisch verdeeld over 2 kolommen, geen vaste waarden
        $specs = [];
        foreach ($features as $f) {
            $t = trim($f['title'] ?? '');
            $v = trim($f['value'] ?? '');
            if ($v !== '') {
                $specs[] = ['label' => $t !== '' ? $t : 'Specificatie', 'value' => $v];
            } elseif ($t !== '') {
                $specs[] = ['label' => $t, 'value' => ''];
            }
        }
        $avgRating = (float) ($product->rating_avg ?? 0);
        $ratingCount = (int) ($product->rating_count ?? 0);
        $displayAvg = $ratingCount > 0 ? number_format($avgRating, 1, '.', '') : '—';
        $isGuestReview = !auth()->check();
    @endphp

    {{-- Exact front from product-details.html — Inter + FontAwesome + slimme tailwind config + original style --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { slimme: { 50: '#f3f7ff', 100: '#e7efff', 200: '#cadcff', 300: '#9dbfff', 400: '#6598ff', 500: '#286dff', 600: '#0757ef', 700: '#0647c9', 800: '#083ca0', 900: '#071d50' } },
                    boxShadow: { soft: '0 10px 35px rgba(15,40,90,.06)', blue: '0 18px 50px rgba(7,87,239,.18)', float: '0 18px 45px rgba(15,35,80,.12)' }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif !important; color: #081737; overflow-x: hidden; background: radial-gradient(circle at 8% 5%, rgba(7, 87, 239, .065), transparent 26%), radial-gradient(circle at 93% 16%, rgba(31, 111, 255, .055), transparent 24%), #f8fafc; }
        .reveal { opacity: 0; transform: translateY(32px); transition: opacity .7s cubic-bezier(.2,.75,.25,1), transform .7s cubic-bezier(.2,.75,.25,1); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .product-stage { position: relative; isolation: isolate; }
        .product-stage::before { content: ""; position: absolute; width: 62%; height: 62%; left: 19%; top: 19%; border-radius: 50%; background: radial-gradient(circle, rgba(67, 133, 255, .17), rgba(67, 133, 255, .07) 40%, transparent 70%); filter: blur(8px); z-index: -1; animation: breathe 4s ease-in-out infinite; }
        .product-stage::after { content: ""; position: absolute; left: 22%; right: 22%; bottom: 10%; height: 24px; border-radius: 50%; background: rgba(18, 50, 110, .15); filter: blur(18px); z-index: -1; }
        @keyframes breathe { 0%, 100% { transform: scale(.95); opacity: .75; } 50% { transform: scale(1.07); opacity: 1; } }
        #mainProductImage { transition: transform .45s cubic-bezier(.2,.8,.2,1), opacity .25s ease, filter .35s ease; filter: drop-shadow(0 25px 25px rgba(21, 40, 85, .14)); }
        .product-stage:hover #mainProductImage { transform: translateY(-6px) scale(1.025); filter: drop-shadow(0 32px 30px rgba(21,40,85,.20)); }
        .thumbnail { transition: all .25s ease; }
        .thumbnail:hover { transform: translateY(-4px); border-color: #75a4ff; }
        .thumbnail.active { border-color: #0757ef; box-shadow: 0 0 0 2px rgba(7,87,239,.08), 0 8px 22px rgba(7,87,239,.13); }
        .choice-badge { animation: badgeFloat 3.5s ease-in-out infinite; }
        @keyframes badgeFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }
        .heart-btn { transition: .3s ease; }
        .heart-btn.active { color: #e11d48; border-color: #fecdd3; background: #fff1f2; }
        .heart-btn.active i { font-weight: 900; }
        .quick-spec { transition: .28s cubic-bezier(.2,.8,.2,1); }
        .quick-spec:hover { transform: translateY(-5px); background: #fff; border-color: #b7d0ff; box-shadow: 0 14px 30px rgba(24, 70, 160, .09); }
        .quick-spec .spec-icon { transition: .3s ease; }
        .quick-spec:hover .spec-icon { transform: rotate(-5deg) scale(1.08); background: #0757ef; color: #fff; }
        .stock-dot { position: relative; }
        .stock-dot::after { content: ""; position: absolute; inset: -4px; border-radius: 999px; border: 1px solid rgba(34,197,94,.55); animation: pulseStock 1.8s ease-out infinite; }
        @keyframes pulseStock { from { transform: scale(.8); opacity: .9; } to { transform: scale(1.8); opacity: 0; } }
        .cart-button { position: relative; overflow: hidden; isolation: isolate; }
        .cart-button::before { content: ""; position: absolute; top: -100%; left: -35%; width: 28%; height: 300%; background: linear-gradient(90deg, transparent, rgba(255,255,255,.32), transparent); transform: rotate(18deg); transition: left .8s ease; }
        .cart-button:hover::before { left: 120%; }
        .cart-button:hover { transform: translateY(-2px); box-shadow: 0 15px 35px rgba(7,87,239,.25); }
        .slimme-section { position: relative; overflow: hidden; isolation: isolate; }
        .slimme-section::before { content: ""; position: absolute; width: 330px; height: 330px; background: rgba(44, 113, 255, .07); border-radius: 50%; left: -100px; top: -190px; z-index: -1; }
        .slimme-section::after { content: ""; position: absolute; width: 300px; height: 300px; background: rgba(44,113,255,.05); border-radius: 50%; right: -100px; bottom: -190px; z-index: -1; }
        .advantage { transition: .25s ease; }
        .advantage:hover { transform: translateX(5px); color: #0757ef; }
        .tab-btn { position: relative; transition: color .25s ease; }
        .tab-btn::after { content: ""; position: absolute; bottom: 0; left: 50%; width: 0; height: 2px; background: #0757ef; transition: .3s ease; transform: translateX(-50%); }
        .tab-btn.active { color: #0757ef; }
        .tab-btn.active::after { width: 70%; }
        .tab-content { animation: fadeTab .35s ease; }
        @keyframes fadeTab { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
        .feature-mini { transition: .25s ease; }
        .feature-mini:hover { transform: translateY(-4px); }
        .spec-row { transition: .2s ease; }
        .spec-row:hover { background: #f6f9ff; padding-left: 8px; padding-right: 8px; }
        .product-card { transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease, border-color .3s ease; }
        .product-card:hover { transform: translateY(-7px); border-color: #bfd5ff; box-shadow: 0 18px 38px rgba(20,54,120,.10); }
        .product-card img, .product-card .product-icon { transition: transform .35s ease; }
        .product-card:hover img, .product-card:hover .product-icon { transform: scale(1.06); }
        .review-card { transition: .3s ease; }
        .review-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(15,40,90,.07); border-color: #d1e0ff; }
        #stickyCart { opacity: 0; pointer-events: none; transform: translateY(100%); transition: .4s cubic-bezier(.2,.8,.2,1); }
        #stickyCart.show { opacity: 1; pointer-events: auto; transform: translateY(0); }
        .float-shape { position: absolute; border-radius: 999px; pointer-events: none; z-index: -1; animation: shapeMove 9s ease-in-out infinite; }
        @keyframes shapeMove { 0%,100% { transform: translateY(0) translateX(0); } 50% { transform: translateY(-18px) translateX(7px); } }
        @media(max-width: 767px) { .product-stage::before { width: 90%; left: 5%; } }
        .shop-card { transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s ease, border-color .3s ease; }
        .shop-card:hover { transform: translateY(-6px); border-color: #bfd3fa; box-shadow: 0 16px 35px rgba(18, 48, 110, .09); }
        .shop-card::after { content: ""; position: absolute; left: 15%; right: 15%; bottom: -1px; height: 2px; border-radius: 20px; background: linear-gradient(90deg, transparent, #0757ef, transparent); transform: scaleX(0); transition: transform .3s ease; }
        .shop-card:hover::after { transform: scaleX(1); }
        /* WYSIWYG description — exact zoals in TinyMCE editor */
        .product-desc-content { font-family: "Figtree", system-ui, -apple-system, sans-serif; font-size: 14px; line-height: 1.7; color: #1e293b; }
        .product-desc-content h2, .product-desc-content h3 { font-weight: 700; color: #0f172a; margin-top: 1.25rem; margin-bottom: 0.5rem; line-height: 1.3; }
        .product-desc-content h2 { font-size: 18px; } .product-desc-content h3 { font-size: 16px; }
        .product-desc-content p { margin-bottom: 0.75rem; }
        .product-desc-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 0.75rem; }
        .product-desc-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 0.75rem; }
        .product-desc-content li { margin-bottom: 0.25rem; }
        .product-desc-content strong, .product-desc-content b { font-weight: 700; color: #0f172a; }
        .product-desc-content em, .product-desc-content i { font-style: italic; }
        .product-desc-content u { text-decoration: underline; }
        .product-desc-content a { color: #2563eb; text-decoration: underline; }
        .product-desc-content blockquote { border-left: 3px solid #e2e8f0; padding-left: 1rem; margin: 1rem 0; color: #64748b; font-style: italic; }
        .product-desc-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 0.75rem 0; }
        .product-desc-content code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
    </style>

    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="float-shape w-[260px] h-[260px] bg-blue-100/30 left-[-100px] top-[500px]"></div>
        <div class="float-shape w-[220px] h-[220px] bg-indigo-100/30 right-[-80px] top-[900px]" style="animation-delay:-3s"></div>
    </div>

    <main class="max-w-[1450px] mx-auto px-4 sm:px-6 lg:px-8 py-7">

        <div class="reveal flex items-center flex-wrap gap-2 text-[13px] text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-slimme-600 transition">Home</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('webshop.category', $category->slug) }}" class="hover:text-slimme-600 transition">Webshop</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <a href="{{ route('webshop.category', $category->slug) }}" class="hover:text-slimme-600 transition">{{ $category->name }}</a>
            <i class="fa-solid fa-chevron-right text-[8px] text-slate-300"></i>
            <span class="text-slate-700 font-medium">{{ $product->title }}</span>
        </div>

        <section id="productHero" class="grid lg:grid-cols-[1.05fr_.95fr] gap-7 xl:gap-12 mb-10">
            <div class="reveal">
                <div class="product-stage bg-gradient-to-br from-white via-[#fbfdff] to-[#f4f8ff] border border-slate-200 rounded-[24px] min-h-[505px] flex items-center justify-center shadow-soft overflow-hidden">
                    @if($product->is_featured)
                    <div class="choice-badge absolute top-5 left-5 z-20 bg-gradient-to-r from-slimme-700 to-slimme-500 text-white px-4 py-2 rounded-xl text-[12px] font-semibold shadow-blue">
                        <i class="fa-solid fa-shield-halved mr-2"></i> Slimme-PC keuze
                    </div>
                    @endif
                    <button type="button" class="absolute top-5 right-5 z-20 w-11 h-11 bg-white/90 backdrop-blur border border-slate-200 rounded-full shadow-md hover:scale-110 hover:text-slimme-600 hover:border-slimme-300 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <button type="button" onclick="previousImage()" class="absolute left-4 z-20 w-11 h-11 rounded-full bg-white/90 backdrop-blur border border-slate-200 shadow-md text-slimme-600 hover:bg-slimme-600 hover:text-white hover:scale-110 transition">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <img id="mainProductImage" src="{{ $resolveImg($gallery[0]) }}" class="w-[78%] max-w-[620px] h-[390px] object-contain" alt="{{ $product->title }}" onerror="this.src='{{ $placeholderSrc }}'">
                    <button type="button" onclick="nextImage()" class="absolute right-4 z-20 w-11 h-11 rounded-full bg-white/90 backdrop-blur border border-slate-200 shadow-md text-slimme-600 hover:bg-slimme-600 hover:text-white hover:scale-110 transition">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <div class="absolute bottom-4 right-5 bg-white/80 backdrop-blur px-3 py-1.5 rounded-full text-[11px] text-slate-500 border border-white">
                        <span id="imageNumber">1</span> / {{ count($gallery) }}
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-3 mt-3">
                    @foreach($gallery as $idx => $img)
                        @if($idx < 5)
                            @php $thumbSrc = $resolveImg($img); @endphp
                            <button type="button" onclick="selectImage({{ $idx }},this)" class="thumbnail {{ $idx===0 ? 'active' : '' }} bg-white {{ $idx===0 ? 'border-2' : 'border border-slate-200' }} rounded-xl h-[93px] p-2">
                                <img src="{{ $thumbSrc }}" class="w-full h-full object-contain" alt="thumb" onerror="this.src='{{ $placeholderSrc }}'">
                            </button>
                        @endif
                    @endforeach
                    @for($i = count($gallery); $i < 5; $i++)
                        <button type="button" class="thumbnail bg-white border border-slate-200 rounded-xl h-[93px] p-2">
                            <div class="h-full flex items-center justify-center">
                                @if($i === 4 && count($gallery) > 5)
                                    <div class="relative flex items-center justify-center w-full h-full">
                                        <i class="fa-solid fa-images text-3xl text-slate-300"></i>
                                        <span class="absolute right-1 bottom-1 bg-slimme-600 text-white text-[9px] px-1.5 py-1 rounded-md">+{{ count($gallery)-5 }}</span>
                                    </div>
                                @else
                                    <i class="fa-solid fa-image text-3xl text-slate-300"></i>
                                @endif
                            </div>
                        </button>
                    @endfor
                </div>
            </div>

            <div class="reveal lg:pt-2" style="transition-delay:.08s">
                <div class="text-[12px] uppercase tracking-[.08em] text-slimme-600 font-bold">
                    {{ $product->brand ?: $category->name }} / {{ $category->name }}
                </div>
                <div class="flex justify-between items-start gap-5 mt-2">
                    <div>
                        <h1 class="font-extrabold text-[30px] md:text-[36px] xl:text-[40px] leading-[1.12] tracking-[-.035em] text-[#071638]">{{ $product->title }}</h1>
                        <div class="flex flex-wrap items-center gap-3 mt-4">
                            <div class="flex gap-[2px] text-amber-400 text-[13px]">
                                @php $full = floor($avgRating); $half = ($avgRating - $full) >= 0.5; @endphp
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= $full)
                                        <i class="fa-solid fa-star"></i>
                                    @elseif($half && $i == $full+1)
                                        <i class="fa-solid fa-star-half-stroke"></i>
                                    @else
                                        <i class="fa-regular fa-star text-slate-300"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm font-semibold text-slate-700">{{ $displayAvg }}</span>
                            <span class="text-xs text-slate-400">({{ $ratingCount }} reviews)</span>
                            <span class="text-slate-300">|</span>
                            <button type="button" onclick="openReviewModal()" class="text-xs font-semibold text-slimme-600 hover:underline">Schrijf een review</button>
                        </div>
                    </div>
                    <button type="button" onclick="toggleFavorite(this)" class="heart-btn shrink-0 w-12 h-12 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-xl hover:scale-110 hover:border-slimme-300 hover:text-slimme-600">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                </div>
                @if(!empty($shortSpecs))<p class="mt-6 text-[14px] text-slate-600 font-medium">{{ $shortSpecs }}</p>@endif
                <div class="mt-6">
                    <div class="flex items-end gap-3">
                        <span class="text-[38px] leading-none font-extrabold tracking-[-.03em] text-[#071638]">€{{ number_format($finalPrice, 2, ',', '.') }}</span>
                        @if($hasDiscount)
                            <span class="text-[14px] text-slate-400 line-through mb-1">€{{ number_format($oldPriceVal, 2, ',', '.') }}</span>
                            @if($product->discount_type === 'percentage')
                                <span class="text-[12px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full mb-1">-{{ (int)$product->discount_value }}%</span>
                            @endif
                        @endif
                    </div>
                    <div class="text-[11px] text-slate-400 mt-2">Inclusief btw</div>
                    @if($product->sku)<div class="text-[11px] text-slate-400">SKU: {{ $product->sku }}</div>@endif
                </div>
                <div class="mt-6 bg-gradient-to-r from-emerald-50/80 via-white to-white border border-emerald-100 rounded-xl px-4 py-3.5">
                    <div class="flex items-center gap-3">
                        <span class="stock-dot w-2.5 h-2.5 rounded-full {{ $inStock ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                        <span class="text-[13px] font-semibold {{ $inStock ? 'text-emerald-700' : 'text-red-600' }}">{{ $inStock ? 'Op voorraad' : 'Niet op voorraad' }}</span>
                    </div>
                    @if(!empty($deliveryText))
                    <div class="flex items-center gap-3 text-[12px] text-slate-600 mt-2.5">
                        <i class="fa-solid fa-truck-fast text-slimme-600"></i> {{ $deliveryText }}
                    </div>
                    @endif
                </div>
                @if(!empty($quickSpecs))
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6">
                    @foreach($quickSpecs as $qs)
                        <div class="quick-spec bg-[#fbfdff] border border-slate-200 rounded-xl p-3.5">
                            <div class="spec-icon w-9 h-9 rounded-lg bg-slimme-50 text-slimme-600 flex items-center justify-center">
                                @if(!empty($qs['icon']))
                                    @if(str_starts_with($qs['icon'], 'fa-') || str_contains($qs['icon'], 'fa '))
                                        <i class="fa-solid {{ $qs['icon'] }}"></i>
                                    @elseif(str_contains($qs['icon'], ' '))
                                        <i class="{{ $qs['icon'] }}"></i>
                                    @else
                                        <i data-lucide="{{ $qs['icon'] }}" class="w-4 h-4"></i>
                                    @endif
                                @else
                                    <i data-lucide="star" class="w-4 h-4"></i>
                                @endif
                            </div>
                            <strong class="block text-[12px] mt-3">{{ $qs['title'] }}</strong>
                            @if(!empty($qs['sub']))<span class="text-[10px] text-slate-400">{{ $qs['sub'] }}</span>@endif
                        </div>
                    @endforeach
                </div>
                @endif
                <div class="flex flex-col sm:flex-row gap-3 mt-7">
                    <div class="flex h-[58px] bg-white border border-slate-200 rounded-xl overflow-hidden">
                        <button type="button" onclick="changeQuantity(-1)" class="w-12 hover:bg-slimme-50 hover:text-slimme-600 transition"><i class="fa-solid fa-minus text-xs"></i></button>
                        <div id="quantity" class="w-12 border-x border-slate-200 flex items-center justify-center font-semibold">1</div>
                        <button type="button" onclick="changeQuantity(1)" class="w-12 hover:bg-slimme-50 hover:text-slimme-600 transition"><i class="fa-solid fa-plus text-xs"></i></button>
                    </div>
                    <button type="button" onclick="addToCart(this)" class="cart-button flex-1 h-[58px] rounded-xl bg-gradient-to-r from-[#0647ca] via-[#0757ef] to-[#2877ff] text-white font-semibold shadow-blue flex items-center justify-center gap-3 transition" {{ !$inStock ? 'disabled' : '' }}>
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-text">{{ $inStock ? 'In winkelwagen' : 'Niet beschikbaar' }}</span>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-3 mt-6 pt-5 border-t border-slate-200">
                    <div class="text-center"><i class="fa-regular fa-circle-check text-emerald-500"></i><div class="text-[11px] font-medium mt-2">2 jaar garantie</div></div>
                    <div class="text-center border-x border-slate-100"><i class="fa-solid fa-location-dot text-slimme-500"></i><div class="text-[11px] font-medium mt-2">Afhalen Apeldoorn</div></div>
                    <div class="text-center"><i class="fa-solid fa-lock text-slimme-500"></i><div class="text-[11px] font-medium mt-2">Veilig betalen</div></div>
                </div>
            </div>
        </section>

        <section class="reveal grid lg:grid-cols-[1.55fr_.65fr] gap-5 mb-6 items-start">
            <div class="bg-white border border-slate-200 rounded-[20px] overflow-hidden shadow-soft">
                <div class="grid grid-cols-2 border-b border-slate-100">
                    <button type="button" onclick="openTab('about',this)" class="tab-btn active py-5 text-[12px] font-semibold">Over dit product</button>
                    <button type="button" onclick="openTab('tabWarranty',this)" class="tab-btn py-5 text-[12px] font-semibold text-slate-500">Levering & garantie</button>
                </div>
                <div id="about" class="tab-content p-6 lg:p-8">
                    @if(!empty($product->description))
                        <div id="descWrapper" class="relative max-w-[790px] overflow-hidden collapsed" style="max-height: 112px; transition: max-height .35s ease;">
                            <div id="descContent" class="product-desc-content">{!! $product->description !!}</div>
                            <div id="descFade" class="pointer-events-none absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-white to-transparent hidden"></div>
                        </div>
                        <button type="button" id="descToggle" class="hidden mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-slimme-600 hover:text-slimme-700 hover:gap-2 transition-all">
                            <span id="descToggleText">Meer weergeven</span>
                            <i id="descToggleIcon" class="fa-solid fa-chevron-down text-[10px] transition-transform"></i>
                        </button>
                    @endif
                    @if(!empty($highlights))
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-7">
                        @foreach($highlights as $h)
                            <div class="feature-mini bg-slate-50/70 rounded-xl p-4">
                                <div class="w-9 h-9 rounded-lg bg-white shadow-sm text-slimme-600 flex items-center justify-center">
                                    @if(!empty($h['icon']))
                                        @if(str_starts_with($h['icon'], 'fa-') || str_contains($h['icon'], 'fa '))
                                            <i class="fa-solid {{ $h['icon'] }}"></i>
                                        @elseif(str_contains($h['icon'], ' '))
                                            <i class="{{ $h['icon'] }}"></i>
                                        @else
                                            <i data-lucide="{{ $h['icon'] }}" class="w-4 h-4"></i>
                                        @endif
                                    @else
                                        <i data-lucide="star" class="w-4 h-4"></i>
                                    @endif
                                </div>
                                <strong class="block text-[11px] mt-3">{{ $h['title'] }}</strong>
                                @if(!empty($h['subtitle']))<span class="text-[10px] text-slate-400">{{ $h['subtitle'] }}</span>@endif
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div id="tabWarranty" class="tab-content hidden p-6 lg:p-8">
                    <div class="space-y-4 text-[13px]">
                        <p><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i> Gratis verzending vanaf €75</p>
                        <p><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i> Afhalen bij Slimme-PC in Apeldoorn mogelijk</p>
                        <p><i class="fa-solid fa-circle-check text-emerald-500 mr-2"></i> 2 jaar garantie</p>
                    </div>
                </div>
            </div>
            <aside class="bg-gradient-to-b from-[#0b2459] to-[#071839] text-white rounded-[20px] p-6 shadow-soft relative overflow-hidden h-fit self-start">
                <div class="absolute w-[180px] h-[180px] bg-blue-400/10 rounded-full -right-20 -top-16"></div>
                <div class="relative">
                    <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center mb-5"><i class="fa-solid fa-truck-fast text-blue-300"></i></div>
                    <h3 class="font-bold text-[16px]">Snel in huis</h3>
                    <p class="text-[11px] text-blue-100/70 mt-2 leading-5">Bestel vandaag en wij zorgen dat jouw laptop zo snel mogelijk onderweg is.</p>
                    <div class="space-y-4 mt-6 text-[11px]">
                        <div class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-[2px]"></i> Gratis verzending vanaf €75</div>
                        <div class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-[2px]"></i> Afhalen in Apeldoorn</div>
                        <div class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-[2px]"></i> 2 jaar garantie</div>
                        <div class="flex gap-3"><i class="fa-solid fa-check text-emerald-400 mt-[2px]"></i> Veilig online betalen</div>
                    </div>
                </div>
            </aside>
        </section>

        @if(!empty($specs))
        <section class="reveal mb-6">
            <div class="bg-white border border-slate-200 rounded-[20px] shadow-soft p-6 lg:p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <span class="text-[10px] uppercase tracking-[.12em] text-slimme-600 font-bold">Technische gegevens</span>
                        <h2 class="font-extrabold text-[20px] mt-1">Specificaties</h2>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slimme-50 text-slimme-600 flex items-center justify-center"><i class="fa-solid fa-sliders"></i></div>
                </div>
                <div class="grid md:grid-cols-2 gap-x-10">
                    @foreach($specs as $row)
                        <div class="spec-row flex justify-between gap-5 py-3 border-b border-slate-100 text-[12px]"><strong>{{ $row['label'] }}</strong><span class="text-slate-500 text-right">{{ $row['value'] }}</span></div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <section class="reveal slimme-section bg-gradient-to-r from-[#f6f9ff] via-white to-[#f4f8ff] border border-blue-100 rounded-[24px] p-6 lg:p-8 mb-6 shadow-soft">
            <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-7 items-center">
                <div class="flex gap-6 items-center">
                    <div class="hidden sm:flex shrink-0 w-[115px] h-[115px] rounded-[30px] bg-gradient-to-br from-[#dce8ff] to-white shadow-inner items-center justify-center rotate-[-4deg]">
                        <div class="w-[67px] h-[67px] bg-white rounded-[22px] shadow-md text-slimme-600 flex items-center justify-center text-[30px] rotate-[4deg]"><i class="fa-solid fa-shield-halved"></i></div>
                    </div>
                    <div>
                        <div class="inline-flex items-center gap-2 bg-blue-50 text-slimme-600 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wide mb-3"><span class="w-1.5 h-1.5 bg-slimme-600 rounded-full"></span> Onze keuze</div>
                        <h2 class="font-extrabold text-[22px] tracking-[-.02em]">Geselecteerd door Slimme-PC</h2>
                        <p class="text-[12px] text-slate-500 mt-1 mb-5">Geen willekeurig model — wij kijken naar prijs, prestaties en betrouwbaarheid.</p>
                        <div class="grid sm:grid-cols-2 gap-x-7 gap-y-3">
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Sterke prijs/prestatie</div>
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Ideaal voor studie & werk</div>
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> 16GB voor soepel multitasken</div>
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Snelle NVMe SSD</div>
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Door ons gecontroleerd</div>
                            <div class="advantage text-[12px] text-slate-700"><i class="fa-solid fa-check text-emerald-500 mr-2"></i> Later uitbreidbaar</div>
                        </div>
                    </div>
                </div>
                <div class="group relative overflow-hidden bg-white border border-blue-100 rounded-[20px] p-5 transition hover:-translate-y-1 hover:shadow-float">
                    <div class="absolute w-[130px] h-[130px] rounded-full bg-blue-50 -right-14 -top-14 group-hover:scale-125 transition duration-500"></div>
                    <div class="relative flex items-center gap-5">
                        <div class="w-[85px] h-[105px] shrink-0 rounded-xl bg-gradient-to-b from-[#e9f0ff] to-[#d8e7ff] flex items-end justify-center overflow-hidden">
                            <i class="fa-solid fa-user-tie text-[68px] text-[#17356d] -mb-1 group-hover:scale-105 transition"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-[15px]">Persoonlijk advies nodig?</h3>
                            <p class="text-[12px] leading-5 text-slate-500 mt-2">Twijfel je of dit de juiste laptop is? Wij helpen je graag persoonlijk.</p>
                            <a href="{{ route('contact') }}" class="inline-flex mt-4 px-4 py-2.5 bg-slimme-50 text-slimme-600 rounded-lg text-xs font-semibold border border-blue-100 hover:bg-slimme-600 hover:text-white transition">Neem contact op <i class="fa-solid fa-arrow-right ml-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="reveal bg-white border border-slate-200 rounded-[20px] shadow-soft p-6 mb-6">
            <div class="flex items-end justify-between gap-5 mb-6">
                <div>
                    <span class="text-[10px] uppercase tracking-[.12em] font-bold text-slimme-600">Handig erbij</span>
                    <h2 class="font-extrabold text-[20px] mt-1">Maak je setup compleet</h2>
                    <p class="text-[12px] text-slate-500 mt-1">Accessoires die goed passen bij deze laptop.</p>
                </div>
                <a href="{{ route('webshop.category', $category->slug) }}" class="hidden sm:flex items-center gap-2 text-[11px] font-semibold text-slimme-600 hover:gap-3 transition-all">Bekijk alle accessoires <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($relatedProducts as $rel)
                    @php
                        $relImgSrc = $resolveImg($rel->main_image ?: ($rel->gallery_images[0] ?? null));
                        $relPrice = $rel->discount_value ? $rel->discounted_price : (float)$rel->price;
                        $relHasDiscount = $rel->old_price && (float)$rel->old_price > (float)$rel->price;
                        $relBadge = $rel->is_featured ? 'Aanrader' : ($relHasDiscount ? 'Aanbieding' : 'Populair');
                        $relBadgeClass = $rel->is_featured ? 'bg-emerald-500' : ($relHasDiscount ? 'bg-red-500' : 'bg-blue-500');
                    @endphp
                    <article class="shop-card group relative bg-white border border-slate-200 rounded-[17px] overflow-hidden">
                        <button type="button" class="absolute z-20 top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full bg-white/90 text-slate-400 hover:text-rose-500 shadow-sm transition"><i class="fa-regular fa-heart"></i></button>
                        <div class="absolute top-3 left-3 z-20 text-[9px] font-bold text-white {{ $relBadgeClass }} px-2.5 py-1 rounded-full">{{ $relBadge }}</div>
                        <a href="{{ route('webshop.product', [$rel->category->slug, $rel->slug]) }}" class="h-[175px] m-3 rounded-[13px] bg-gradient-to-br from-[#f5f7fb] to-[#eef2f8] flex items-center justify-center overflow-hidden block">
                            <img src="{{ $relImgSrc }}" class="w-[82%] h-[82%] object-contain transition duration-300 group-hover:scale-[1.06]" alt="{{ $rel->title }}" onerror="this.src='{{ $placeholderSrc }}'">
                        </a>
                        <div class="px-4 pb-4">
                            <a href="{{ route('webshop.product', [$rel->category->slug, $rel->slug]) }}"><h3 class="text-[12px] leading-[18px] font-bold text-[#071638] min-h-[36px] hover:text-slimme-600 transition">{{ $rel->title }}</h3></a>
                            @php
                                $relFeats = is_array($rel->features) ? $rel->features : [];
                                $relFeatStrs = array_map(function($f){ if(is_array($f) && isset($f['value'])){ $t=trim($f['title']??''); $v=trim($f['value']); return $t!==''? $t.': '.$v : $v; } return (string)$f; }, $relFeats);
                                $relFeatStrs = array_values(array_filter($relFeatStrs));
                            @endphp
                            <p class="text-[10px] text-slate-400 mt-1">{{ $rel->brand ?: $category->name }}@if(!empty($relFeatStrs)) · {{ Str::limit(implode(' · ', array_slice($relFeatStrs,0,2)), 30) }}@endif</p>
                            <div class="mt-4"><div class="text-[18px] font-extrabold text-[#071638]">€{{ number_format($relPrice, 2, ',', '.') }}</div></div>
                            <div class="flex items-center justify-between mt-4">
                                <div class="text-[10px] text-emerald-600 font-semibold"><i class="fa-solid fa-circle text-[6px] mr-1"></i> Op voorraad</div>
                                <a href="{{ route('webshop.product', [$rel->category->slug, $rel->slug]) }}" class="w-9 h-9 rounded-lg bg-slimme-600 text-white flex items-center justify-center hover:bg-slimme-700 hover:scale-105 shadow-md transition"><i class="fa-solid fa-cart-shopping text-[12px]"></i></a>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="shop-card group relative bg-white border border-slate-200 rounded-[17px] overflow-hidden">
                        <div class="h-[175px] m-3 rounded-[13px] bg-gradient-to-br from-[#f5f7fb] to-[#eef2f8] flex items-center justify-center"><i class="fa-solid fa-plug-circle-bolt text-[74px] text-slate-700"></i></div>
                        <div class="px-4 pb-4"><h3 class="text-[12px] font-bold text-[#071638]">USB-C Multiport Adapter</h3><p class="text-[10px] text-slate-400">USB-C · HDMI · USB-A</p><div class="mt-4 text-[18px] font-extrabold">€39,00</div></div>
                    </article>
                    <article class="shop-card group relative bg-white border border-slate-200 rounded-[17px] overflow-hidden">
                        <div class="h-[175px] m-3 rounded-[13px] bg-gradient-to-br from-[#f5f7fb] to-[#eef2f8] flex items-center justify-center"><i class="fa-solid fa-computer-mouse text-[82px] text-slate-700"></i></div>
                        <div class="px-4 pb-4"><h3 class="text-[12px] font-bold text-[#071638]">Logitech M650 draadloze muis</h3><p class="text-[10px] text-slate-400">Bluetooth · Stil · Ergonomisch</p><div class="mt-4 text-[18px] font-extrabold">€24,00</div></div>
                    </article>
                    <article class="shop-card group relative bg-white border border-slate-200 rounded-[17px] overflow-hidden">
                        <div class="h-[175px] m-3 rounded-[13px] bg-gradient-to-br from-[#f5f7fb] to-[#eef2f8] flex items-center justify-center"><i class="fa-solid fa-headphones text-[78px] text-slate-700"></i></div>
                        <div class="px-4 pb-4"><h3 class="text-[12px] font-bold text-[#071638]">Trust GXT 491 FAYZO Headset</h3><p class="text-[10px] text-slate-400">Gaming · Microfoon · Stereo</p><div class="mt-4 text-[18px] font-extrabold">€49,00</div></div>
                    </article>
                    <article class="shop-card group relative bg-white border border-slate-200 rounded-[17px] overflow-hidden">
                        <div class="h-[175px] m-3 rounded-[13px] bg-gradient-to-br from-[#f5f7fb] to-[#eef2f8] flex items-center justify-center"><img src="https://images.unsplash.com/photo-1625842268584-8f3296236761?auto=format&fit=crop&w=500&q=80" class="w-[82%] h-[82%] object-contain" alt="sleeve"></div>
                        <div class="px-4 pb-4"><h3 class="text-[12px] font-bold text-[#071638]">Laptop sleeve 15.6 inch</h3><p class="text-[10px] text-slate-400">15.6" · Bescherming · Zwart</p><div class="mt-4 text-[18px] font-extrabold">€29,00</div></div>
                    </article>
                @endforelse
            </div>
        </section>

    </main>

    {{-- Review modal — zelfde design systeem (slimme gradient, rounded-[20px], Inter) --}}
    <div id="reviewModal" class="fixed inset-0 z-[70] hidden">
        <div id="reviewModalOverlay" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative flex min-h-screen items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-[20px] bg-white shadow-2xl border border-slate-200 overflow-hidden max-h-[90vh] overflow-y-auto">
                <div class="sticky top-0 bg-white flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="text-[16px] font-extrabold text-[#071638]">Schrijf een review</h3>
                    <button type="button" onclick="closeReviewModal()" class="h-8 w-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="reviewForm" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="rating" id="reviewRating" value="">
                    <div>
                        <label class="text-xs font-bold text-slate-700">Beoordeling *</label>
                        <div id="starPicker" class="mt-2 flex gap-1.5">
                            @for($i=1;$i<=5;$i++)
                                <button type="button" data-star="{{ $i }}" class="star-btn h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-300 hover:text-amber-400 hover:border-amber-300 hover:bg-amber-50 flex items-center justify-center transition">
                                    <i class="fa-solid fa-star text-[18px]"></i>
                                </button>
                            @endfor
                        </div>
                        <p id="ratingError" class="hidden mt-1.5 text-xs font-medium text-red-500">Kies een beoordeling (1-5 sterren)</p>
                    </div>
                    @if($isGuestReview)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div><label class="text-xs font-bold text-slate-700">Naam *</label><input type="text" name="guest_name" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Je naam"></div>
                        <div><label class="text-xs font-bold text-slate-700">E-mail *</label><input type="email" name="guest_email" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="je@email.nl"></div>
                    </div>
                    @else
                    <div><label class="text-xs font-bold text-slate-700">Naam *</label><input type="text" name="guest_name" required class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Jouw naam"></div>
                    @endif
                    <div><label class="text-xs font-bold text-slate-700">Jouw ervaring *</label><textarea name="body" required rows="4" minlength="10" maxlength="1000" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-4 focus:ring-blue-100" placeholder="Vertel over je ervaring met dit product... (min. 10 tekens)"></textarea><p class="mt-1 text-[11px] text-slate-400"><span id="bodyCount">0</span>/1000</p></div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="closeReviewModal()" class="flex-1 rounded-xl border border-slate-200 py-2.5 text-sm font-bold hover:bg-slate-50">Annuleren</button>
                        <button type="submit" id="reviewSubmitBtn" class="flex-1 rounded-xl bg-gradient-to-r from-slimme-700 to-slimme-500 py-2.5 text-sm font-bold text-white shadow-blue hover:opacity-90 flex items-center justify-center gap-2"><span>Versturen</span><svg id="reviewSpinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" class="opacity-75"/></svg></button>
                    </div>
                    <p class="text-[11px] text-slate-400 text-center">Je review wordt eerst beoordeeld door Slimme-PC. Het gemiddelde cijfer wordt daarna bijgewerkt.</p>
                </form>
            </div>
        </div>
    </div>

    <div id="reviewToast" class="fixed bottom-5 right-5 z-[80] hidden max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg"></div>

    <div id="stickyCart" class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200 shadow-[0_-15px_40px_rgba(20,40,80,.10)]">
        <div class="max-w-[1450px] mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-5">
            <div class="hidden md:flex items-center gap-4 flex-1">
                <div class="w-[68px] h-[52px] bg-slate-50 rounded-lg flex items-center justify-center overflow-hidden">
                    <img src="{{ $resolveImg($gallery[0]) }}" class="w-full h-full object-contain p-1" alt="{{ $product->title }}" onerror="this.src='{{ $placeholderSrc }}'">
                </div>
                <div>
                    <strong class="text-[12px]">{{ $product->title }}</strong>
                    @if(!empty($shortSpecs))<div class="text-[10px] text-slate-400 mt-1">{{ Str::limit($shortSpecs, 50) }}</div>@endif
                </div>
            </div>
            <div class="hidden sm:block min-w-[125px]">
                <strong class="text-[18px]">€{{ number_format($finalPrice, 2, ',', '.') }}</strong>
                <div class="text-[10px] {{ $inStock ? 'text-emerald-600' : 'text-red-500' }} font-semibold mt-1">● {{ $inStock ? 'Op voorraad' : 'Niet op voorraad' }}</div>
            </div>
            <button type="button" class="cart-button h-11 flex-1 md:flex-none md:min-w-[260px] px-6 bg-gradient-to-r from-slimme-700 to-slimme-500 text-white rounded-lg text-sm font-semibold flex items-center justify-center gap-3">
                <i class="fa-solid fa-cart-shopping"></i> In winkelwagen
            </button>
            <button type="button" onclick="toggleFavorite(this)" class="heart-btn w-11 h-11 shrink-0 border border-slate-200 rounded-lg bg-white hover:text-slimme-600 transition"><i class="fa-regular fa-heart"></i></button>
        </div>
    </div>

    <script>
        const images = @json(array_map(fn($p) => $resolveImg($p), $gallery));
        let imageIndex = 0;
        function updateImage() {
            const image = document.getElementById("mainProductImage");
            if (!image) return;
            image.style.opacity = "0"; image.style.transform = "translateY(5px) scale(.97)";
            setTimeout(() => {
                image.src = images[imageIndex];
                const numEl = document.getElementById("imageNumber");
                if (numEl) numEl.innerText = imageIndex + 1;
                image.style.opacity = "1"; image.style.transform = "";
            }, 180);
            document.querySelectorAll(".thumbnail").forEach((thumb,index) => { thumb.classList.toggle("active", index === imageIndex); });
        }
        function nextImage() { imageIndex++; if(imageIndex >= images.length) imageIndex = 0; updateImage(); }
        function previousImage() { imageIndex--; if(imageIndex < 0) imageIndex = images.length - 1; updateImage(); }
        function selectImage(index){ imageIndex = index; updateImage(); }
        let qty = 1;
        function changeQuantity(amount) { qty += amount; if(qty < 1) qty = 1; const el=document.getElementById("quantity"); if(el) el.innerText = qty; }
        function toggleFavorite(button) { button.classList.toggle("active"); const icon = button.querySelector("i"); if(!icon) return; if(button.classList.contains("active")) { icon.classList.remove("fa-regular"); icon.classList.add("fa-solid"); } else { icon.classList.remove("fa-solid"); icon.classList.add("fa-regular"); } }
        function addToCart(button) { const text = button.querySelector(".cart-text"); if(!text) return; const original = text.innerText; text.innerText = "Toegevoegd"; const icon = button.querySelector("i"); const origIcon = icon ? icon.className : ""; if(icon) icon.className = "fa-solid fa-check"; setTimeout(() => { text.innerText = original; if(icon) icon.className = origIcon || "fa-solid fa-cart-shopping"; }, 1500); }
        function openTab(tabId,button) { document.querySelectorAll(".tab-content").forEach(tab => tab.classList.add("hidden")); document.querySelectorAll(".tab-btn").forEach(tab => { tab.classList.remove("active"); tab.classList.add("text-slate-500"); }); const target=document.getElementById(tabId); if(target) target.classList.remove("hidden"); button.classList.add("active"); button.classList.remove("text-slate-500"); }
        const observer = new IntersectionObserver(entries => { entries.forEach(entry => { if(entry.isIntersecting){ entry.target.classList.add("visible"); observer.unobserve(entry.target); } }); }, { threshold: .1 });
        document.querySelectorAll(".reveal").forEach(element => { observer.observe(element); });
        const hero = document.getElementById("productHero");
        const sticky = document.getElementById("stickyCart");
        if(hero && sticky){ const stickyObserver = new IntersectionObserver(entries => { const entry = entries[0]; if(!entry.isIntersecting && entry.boundingClientRect.top < 0) sticky.classList.add("show"); else sticky.classList.remove("show"); }, { threshold: 0 }); stickyObserver.observe(hero); }
        setTimeout(()=>{ document.querySelectorAll('.reveal').forEach(el=>{ const r=el.getBoundingClientRect(); if(r.top < window.innerHeight) el.classList.add('visible'); }); }, 100);
        // Over dit product — 4 regels + Meer weergeven
        (function(){
            const wrapper = document.getElementById('descWrapper');
            const content = document.getElementById('descContent');
            const btn = document.getElementById('descToggle');
            const fade = document.getElementById('descFade');
            const txt = document.getElementById('descToggleText');
            const icon = document.getElementById('descToggleIcon');
            if(!wrapper || !content || !btn) return;
            function check(){
                const isClamped = content.scrollHeight > wrapper.clientHeight + 8;
                if(isClamped || wrapper.classList.contains('expanded')){
                    btn.classList.remove('hidden');
                    if(!wrapper.classList.contains('expanded') && fade) fade.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                    if(fade) fade.classList.add('hidden');
                }
            }
            // init collapsed
            wrapper.classList.add('collapsed');
            requestAnimationFrame(() => {
                // if content is short, expand directly
                if(content.scrollHeight <= 120){
                    wrapper.style.maxHeight = 'none';
                    wrapper.classList.remove('collapsed');
                    wrapper.classList.add('expanded');
                    btn.classList.add('hidden');
                    if(fade) fade.classList.add('hidden');
                } else {
                    check();
                }
            });
            btn.addEventListener('click', () => {
                const isExpanded = wrapper.classList.contains('expanded');
                if(isExpanded){
                    wrapper.style.maxHeight = '112px';
                    wrapper.classList.remove('expanded');
                    wrapper.classList.add('collapsed');
                    if(txt) txt.textContent = 'Meer weergeven';
                    if(icon){ icon.classList.remove('fa-chevron-up'); icon.classList.add('fa-chevron-down'); icon.style.transform=''; }
                    if(fade) fade.classList.remove('hidden');
                    wrapper.scrollIntoView({behavior:'smooth', block:'nearest'});
                } else {
                    wrapper.style.maxHeight = content.scrollHeight + 24 + 'px';
                    wrapper.classList.add('expanded');
                    wrapper.classList.remove('collapsed');
                    if(txt) txt.textContent = 'Minder weergeven';
                    if(icon){ icon.classList.remove('fa-chevron-down'); icon.classList.add('fa-chevron-up'); }
                    if(fade) fade.classList.add('hidden');
                }
            });
            window.addEventListener('resize', check);
        })();
        // Review modal — same design system
        window.openReviewModal = function(){
            const m = document.getElementById('reviewModal');
            if(!m) return;
            m.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // reset stars if not selected
        };
        window.closeReviewModal = function(){
            const m = document.getElementById('reviewModal');
            if(!m) return;
            m.classList.add('hidden');
            document.body.style.overflow = '';
        };
        document.getElementById('reviewModalOverlay')?.addEventListener('click', closeReviewModal);
        document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeReviewModal(); });
        (function(){
            const stars = document.querySelectorAll('#starPicker .star-btn');
            const input = document.getElementById('reviewRating');
            const err = document.getElementById('ratingError');
            function setRating(n){
                if(input) input.value = n;
                stars.forEach((b,i)=>{
                    const active = i < n;
                    b.classList.toggle('bg-amber-50', active);
                    b.classList.toggle('border-amber-300', active);
                    b.classList.toggle('text-amber-400', active);
                    b.classList.toggle('text-slate-300', !active);
                    b.classList.toggle('bg-white', !active);
                });
                if(err) err.classList.add('hidden');
            }
            stars.forEach(b=>{
                b.addEventListener('click', ()=> setRating(parseInt(b.getAttribute('data-star'))));
            });
            const body = document.querySelector('#reviewForm textarea[name="body"]');
            const cnt = document.getElementById('bodyCount');
            if(body && cnt){
                body.addEventListener('input', ()=> cnt.textContent = body.value.length);
            }
            const form = document.getElementById('reviewForm');
            const btn = document.getElementById('reviewSubmitBtn');
            const spinner = document.getElementById('reviewSpinner');
            const toast = document.getElementById('reviewToast');
            function showToast(msg, ok=true){
                if(!toast) return;
                toast.textContent = msg;
                toast.className = 'fixed bottom-5 right-5 z-[80] max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg ' + (ok ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white');
                toast.classList.remove('hidden');
                setTimeout(()=> toast.classList.add('hidden'), 4000);
            }
            if(form){
                form.addEventListener('submit', async (e)=>{
                    e.preventDefault();
                    if(!input || !input.value){
                        if(err) err.classList.remove('hidden');
                        showToast('Kies eerst een beoordeling', false);
                        return;
                    }
                    const fd = new FormData(form);
                    const token = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.content || '';
                    if(btn){ btn.disabled=true; btn.querySelector('span').textContent='Versturen...'; if(spinner) spinner.classList.remove('hidden'); }
                    try{
                        const res = await fetch(@json(route('webshop.reviews.store', [$category->slug, $product->slug])), {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: fd
                        });
                        const data = await res.json();
                        if(!res.ok) throw new Error(data.message || Object.values(data.errors||{}).flat().join(', ') || 'Fout');
                        showToast(data.message || 'Bedankt! Je review is ontvangen.', true);
                        form.reset();
                        setRating(0);
                        if(cnt) cnt.textContent='0';
                        setTimeout(closeReviewModal, 1200);
                    }catch(err){
                        showToast(err.message || 'Er ging iets mis', false);
                    }finally{
                        if(btn){ btn.disabled=false; btn.querySelector('span').textContent='Versturen'; if(spinner) spinner.classList.add('hidden'); }
                    }
                });
            }
        })();
        if(window.lucide) lucide.createIcons();
        if(window.__lucideRefresh) window.__lucideRefresh();
    </script>

    @include('landing.partials.footer')
    @include('landing.partials.floating')
@endsection
