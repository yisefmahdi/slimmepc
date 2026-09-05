@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    @php
        $isEmpty = $cart->items->isEmpty();
        $placeholderSrc = asset('assets/img/product-placeholder.jpg');
        $resolveImg = function($p) use ($placeholderSrc) {
            if (!$p) return $placeholderSrc;
            if (str_starts_with($p, 'http')) return $p;
            if (str_starts_with($p, 'assets/')) return asset($p);
            if (str_starts_with($p, 'storage/')) return asset($p);
            return asset('storage/' . ltrim($p, '/'));
        };
        // Try to find first category for Verder winkelen
        $shopLink = route('home');
        if(isset($cart) && $cart->items->isNotEmpty()){
            $firstCat = $cart->items->first()->product?->category;
            if($firstCat) $shopLink = route('webshop.category', $firstCat->slug);
        } else {
            $firstActiveCat = \App\Models\Category::where('status', true)->orderBy('sort_order')->first();
            if($firstActiveCat) $shopLink = route('webshop.category', $firstActiveCat->slug);
        }
    @endphp

    {{-- Exact design from winkelwagen.html — Inter + Tailwind CDN config + lucide --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        slimme: { blue: '#0759F5', dark: '#07173A', text: '#152A57', light: '#F5F8FF', border: '#DCE4F0', green: '#0BA15B' }
                    },
                    boxShadow: { card: '0 8px 30px rgba(15,40,90,.06)', soft: '0 4px 18px rgba(15,40,90,.05)', button: '0 8px 20px rgba(7,89,245,.20)' }
                }
            }
        }
    </script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        button, a { transition: all .2s ease; }
        .quantity-button:active { transform: scale(.92); }
        .product-row { transition: opacity .25s ease, transform .25s ease; }
        .product-row.removing { opacity: 0; transform: translateX(-15px); }
        /* Fix modals on landing (same as admin siblings — landing.css lacks --c-card so overlay/panel were transparent) */
        #modal-generic-confirm [data-modal-overlay],
        #modal-clearCartModal [data-modal-overlay],
        #modal-removeCartItemModal [data-modal-overlay] { background: rgba(15,23,42,0.62) !important; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
        #modal-generic-confirm .modal-panel-anim { background: #fff !important; border-color: rgba(148,163,184,.25) !important; }
    </style>

    <main class="min-h-screen bg-white text-[#07173A]">
        <section class="max-w-[1240px] mx-auto px-4 sm:px-5 md:px-8 pt-6 sm:pt-7 pb-10 sm:pb-16">

            {{-- TOP --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-5 mb-6 sm:mb-7">
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <h1 class="text-[26px] sm:text-[32px] md:text-[35px] font-bold tracking-tight text-[#07173A] leading-none">Winkelwagen</h1>
                        <div class="relative w-[34px] h-[34px] sm:w-[37px] sm:h-[37px] rounded-[10px] border border-[#DDE6F4] bg-[#F5F8FF] flex items-center justify-center shrink-0">
                            <i data-lucide="shopping-cart" class="w-[18px] h-[18px] sm:w-[20px] sm:h-[20px] text-[#0759F5]"></i>
                            <span id="cartCountBadge" data-cart-count
                                class="absolute -top-1.5 -right-1 sm:-top-2 sm:-right-1 min-w-[18px] h-[18px] sm:min-w-[19px] sm:h-[19px] px-1 rounded-full bg-[#0759F5] text-white text-[10px] sm:text-[11px] font-semibold flex items-center justify-center">
                                {{ $totals['count'] }}
                            </span>
                        </div>
                    </div>
                    <p class="text-[13px] sm:text-[15px] text-[#3E547F] mt-1.5 leading-5">Controleer je producten en rond je bestelling af.</p>
                </div>
                <a href="{{ $shopLink }}" class="inline-flex items-center gap-2 sm:gap-3 text-[#0759F5] font-semibold text-[13px] sm:text-[14px] mt-1 hover:gap-3 sm:hover:gap-4 transition-all shrink-0">
                    <i data-lucide="arrow-left" class="w-[16px] h-[16px] sm:w-[18px] sm:h-[18px]"></i>
                    Verder winkelen
                </a>
            </div>

            @if($isEmpty)
                {{-- EMPTY STATE --}}
                <div class="rounded-[11px] border-2 border-dashed border-[#DCE4EF] bg-[#F8FAFF] p-8 sm:p-12 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white border border-[#DDE6F4] text-[#0759F5]">
                        <i data-lucide="shopping-cart" class="w-8 h-8"></i>
                    </div>
                    <h3 class="mt-4 text-[18px] font-bold text-[#07173A]">Je winkelwagen is leeg</h3>
                    <p class="mt-2 text-[14px] text-[#3E547F]">Voeg producten toe om te beginnen met winkelen.</p>
                    <a href="{{ $shopLink }}" class="mt-6 inline-flex items-center gap-2 rounded-[6px] bg-[#0759F5] px-6 py-3 text-[14px] font-semibold text-white hover:bg-[#064ED4] transition">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i> Naar de webshop
                    </a>
                </div>
            @else

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_375px] gap-5 sm:gap-7 items-start">

                {{-- LEFT --}}
                <div class="space-y-5 sm:space-y-6">

                    {{-- CART CARD --}}
                    <div class="bg-white border border-[#DCE4EF] rounded-[11px] shadow-[0_8px_30px_rgba(15,40,90,.06)] overflow-hidden">

                        {{-- HEAD --}}
                        <div class="hidden md:grid md:grid-cols-[minmax(0,1fr)_110px_150px_125px_48px] items-center gap-2 px-6 py-[17px] border-b border-[#DFE6EF] text-[12px] font-semibold text-[#0A1A3D]">
                            <div>Product</div>
                            <div>Prijs</div>
                            <div class="text-center">Aantal</div>
                            <div class="text-center">Subtotaal</div>
                            <div></div>
                        </div>

                        {{-- PRODUCTS --}}
                        <div id="cartProducts">
                        @foreach($cart->items as $item)
                            @php
                                $product = $item->product;
                                $pImg = $product ? ($product->main_image ?: ($product->gallery_images[0] ?? null)) : null;
                                $imgSrc = $resolveImg($pImg);
                                $inStock = $product && $product->stock_status === 'in_stock';
                                // Build specs string
                                $specs = '';
                                if($product && !empty($product->features) && is_array($product->features)){
                                    $featStrs = array_map(function($f){
                                        if(is_array($f) && isset($f['value'])){ $t=trim($f['title']??''); $v=trim($f['value']); return $t!=='' ? $t.': '.$v : $v; }
                                        return (string)$f;
                                    }, $product->features);
                                    $featStrs = array_values(array_filter($featStrs));
                                    if(!empty($featStrs)) $specs = implode(' • ', array_slice($featStrs, 0, 3));
                                }
                                if($specs === '' && $product){
                                    $specs = $product->brand ?: ($product->category->name ?? '');
                                }
                            @endphp
                            <div id="product-{{ $item->id }}" class="product-row" data-price="{{ $item->price_snapshot }}">
                                <div class="grid grid-cols-2 md:grid-cols-[minmax(0,1fr)_110px_150px_125px_48px] items-center gap-3 md:gap-2 px-4 sm:px-5 md:px-6 py-4 sm:py-5 border-b border-[#DFE6EF] last:border-0">
                                    {{-- PRODUCT INFO — full width on mobile --}}
                                    <div class="col-span-2 md:col-span-1 flex items-center gap-3 sm:gap-5 min-w-0">
                                        <div class="w-[84px] h-[64px] sm:w-[96px] sm:h-[76px] md:w-[108px] md:h-[84px] rounded-[6px] bg-[#F3F5F8] overflow-hidden flex items-center justify-center shrink-0">
                                            <a href="{{ $product ? route('webshop.product', [$product->category->slug, $product->slug]) : '#' }}">
                                                <img src="{{ $imgSrc }}" alt="{{ $product->title ?? 'Product' }}" class="w-full h-full object-cover" onerror="this.src='{{ $placeholderSrc }}'">
                                            </a>
                                        </div>
                                        <div class="min-w-0">
                                            <a href="{{ $product ? route('webshop.product', [$product->category->slug, $product->slug]) : '#' }}">
                                                <h3 class="text-[14px] leading-5 font-semibold text-[#081A40] hover:text-[#0759F5] transition line-clamp-2">{{ $product->title ?? 'Onbekend product' }}</h3>
                                            </a>
                                            @if($specs)
                                            <p class="mt-1 text-[13px] leading-5 text-[#3D527D] line-clamp-1">{{ $specs }}</p>
                                            @endif
                                            <div class="flex items-center gap-2 mt-1.5 text-[12px] {{ $inStock ? 'text-[#087F49]' : 'text-red-500' }}">
                                                <span class="w-[8px] h-[8px] rounded-full {{ $inStock ? 'bg-[#08AF5A]' : 'bg-red-500' }}"></span>
                                                {{ $inStock ? 'Op voorraad' : 'Niet op voorraad' }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PRICE --}}
                                    <div class="col-span-1">
                                        <span class="md:hidden block mb-1 text-[11px] text-[#7685A4]">Prijs</span>
                                        <span class="text-[15px] sm:text-[17px] font-bold whitespace-nowrap text-[#07163B]">€{{ number_format($item->price_snapshot, 2, ',', '.') }}</span>
                                    </div>

                                    {{-- QUANTITY --}}
                                    <div class="col-span-1 flex justify-end md:justify-center">
                                        <div class="inline-flex h-[40px] sm:h-[43px] rounded-[6px] border border-[#D6DFEB] overflow-hidden">
                                            <button onclick="changeCartQuantity({{ $item->id }}, -1)" class="quantity-button w-[38px] sm:w-[43px] flex items-center justify-center hover:bg-[#F5F7FA] transition">
                                                <i data-lucide="minus" class="w-[15px] h-[15px] sm:w-[16px] sm:h-[16px]"></i>
                                            </button>
                                            <div id="quantity-{{ $item->id }}" class="w-[38px] sm:w-[43px] border-x border-[#DFE5ED] flex items-center justify-center font-semibold text-[13px] sm:text-[14px]">{{ $item->quantity }}</div>
                                            <button onclick="changeCartQuantity({{ $item->id }}, 1)" class="quantity-button w-[38px] sm:w-[43px] flex items-center justify-center hover:bg-[#F5F7FA] transition">
                                                <i data-lucide="plus" class="w-[15px] h-[15px] sm:w-[16px] sm:h-[16px]"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- SUBTOTAL --}}
                                    <div class="col-span-1 md:text-center">
                                        <span class="md:hidden block mb-1 text-[11px] text-[#7685A4]">Subtotaal</span>
                                        <span id="subtotal-{{ $item->id }}" class="text-[15px] sm:text-[17px] font-bold whitespace-nowrap text-[#07163B]">€{{ number_format($item->price_snapshot * $item->quantity, 2, ',', '.') }}</span>
                                    </div>

                                    {{-- DELETE --}}
                                    <div class="col-span-1 flex justify-end md:justify-center">
                                        <button onclick="confirmRemoveCartProduct({{ $item->id }}, this)" data-product-title="{{ $product->title ?? 'dit product' }}" aria-label="Product verwijderen" class="w-[38px] h-[38px] sm:w-[42px] sm:h-[42px] shrink-0 rounded-[7px] border border-[#D7E0EC] flex items-center justify-center text-[#29446F] bg-white hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition">
                                            <i data-lucide="trash-2" class="w-[16px] h-[16px] sm:w-[18px] sm:h-[18px]"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>

                        {{-- FOOT --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2.5 sm:gap-3 px-4 sm:px-5 md:px-6 py-3 sm:py-[13px] border-t border-[#DFE6EF]">
                            <button onclick="clearCart()" class="w-full sm:w-auto h-[40px] sm:h-[37px] px-4 rounded-[6px] border border-[#D6DFEC] text-[12px] font-medium text-[#243D69] inline-flex items-center justify-center gap-2 hover:bg-[#F6F8FB] transition">
                                <i data-lucide="trash-2" class="w-[16px] h-[16px]"></i> Winkelwagen legen
                            </button>
                            <a href="{{ $shopLink }}" class="w-full sm:w-auto h-[40px] sm:h-[37px] px-4 rounded-[6px] border border-[#D6DFEC] text-[12px] font-medium text-[#243D69] inline-flex items-center justify-center gap-2 hover:bg-[#F6F8FB] transition">
                                <i data-lucide="refresh-cw" class="w-[16px] h-[16px]"></i> Verder winkelen
                            </a>
                        </div>
                    </div>

                    {{-- UPSELL --}}
                    @if($upsell)
                    @php
                        $upsImg = $upsell->main_image ?: ($upsell->gallery_images[0] ?? null);
                        $upsSrc = $resolveImg($upsImg);
                    @endphp
                    <div class="bg-white border border-[#DFE6F0] rounded-[10px] shadow-[0_4px_18px_rgba(15,40,90,.05)] px-4 sm:px-5 py-4">
                        <div class="grid lg:grid-cols-[280px_minmax(0,1fr)] gap-6 items-center">
                            <div>
                                <h2 class="text-[16px] font-bold text-[#081A40]">Misschien handig erbij</h2>
                                <p class="text-[13px] leading-6 text-[#41577F] mt-1.5">Maak je setup compleet met deze populaire accessoires.</p>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center gap-4 lg:border-l lg:border-[#E3E8F0] lg:pl-7">
                                <div class="w-full sm:w-[115px] h-[90px] border border-[#DDE4ED] rounded-[6px] bg-[#F6F8FA] overflow-hidden shrink-0 flex items-center justify-center">
                                    <a href="{{ route('webshop.product', [$upsell->category->slug, $upsell->slug]) }}">
                                        <img src="{{ $upsSrc }}" alt="{{ $upsell->title }}" class="w-full h-full object-cover" onerror="this.src='{{ $placeholderSrc }}'">
                                    </a>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-[13px] font-semibold text-[#091A3E] line-clamp-1">{{ $upsell->title }}</h3>
                                    <p class="text-[12px] text-[#42577C] mt-1 line-clamp-1">{{ $upsell->brand ?: $upsell->category->name }}</p>
                                    <div class="flex items-center justify-between gap-4 mt-4">
                                        <span class="text-[16px] font-bold text-[#07163B]">€{{ number_format($upsell->discounted_price, 2, ',', '.') }}</span>
                                        <x-add-to-cart :product="$upsell" variant="upsell" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- OVERZICHT --}}
                <aside class="xl:sticky xl:top-6 bg-white border border-[#DDE5EF] rounded-[11px] shadow-[0_8px_30px_rgba(15,40,90,.06)] px-4 sm:px-6 py-5 sm:py-7">
                    <h2 class="text-[21px] font-bold text-[#07163B] mb-6">Overzicht</h2>
                    <div class="h-px bg-[#DEE5EE] mb-6"></div>

                    <div class="flex items-center justify-between gap-4">
                        <span id="summaryProductLabel" class="text-[13px] text-[#354D78]">Subtotaal ({{ $totals['count'] }} {{ $totals['count'] === 1 ? 'product' : 'producten' }})</span>
                        <span id="summarySubtotal" class="text-[14px] font-semibold text-[#07163B]">€{{ number_format($totals['subtotal'], 2, ',', '.') }}</span>
                    </div>

                    <div class="h-px bg-[#DEE5EE] my-6"></div>

                    <div class="flex items-start justify-between gap-5">
                        <div>
                            <h3 class="text-[17px] font-bold text-[#07163B]">Totaal</h3>
                            <p class="text-[11px] text-[#536789] mt-1">Incl. btw</p>
                        </div>
                        <span id="summaryTotal" class="text-[24px] font-bold tracking-tight text-[#07163B]">€{{ number_format($totals['subtotal'], 2, ',', '.') }}</span>
                    </div>

                    <a href="#" onclick="event.preventDefault(); toast('Afrekenen volgt binnenkort.', 'success');" class="group mt-7 w-full h-[54px] rounded-[6px] bg-[#0759F5] hover:bg-[#064ED4] shadow-[0_8px_20px_rgba(7,89,245,.20)] px-5 flex items-center justify-between text-white transition">
                        <div class="flex items-center gap-4">
                            <i data-lucide="lock-keyhole" class="w-[20px] h-[20px]"></i>
                            <span class="text-[15px] font-semibold">Doorgaan naar afrekenen</span>
                        </div>
                        <i data-lucide="arrow-right" class="w-[19px] h-[19px] transition-transform group-hover:translate-x-1"></i>
                    </a>

                    <div class="space-y-4 mt-7">
                        <div class="flex items-center gap-3 text-[12px] text-[#354E78]"><i data-lucide="check" class="w-[16px] h-[16px] stroke-[2.5] text-[#059856]"></i> Veilig betalen</div>
                        <div class="flex items-center gap-3 text-[12px] text-[#354E78]"><i data-lucide="check" class="w-[16px] h-[16px] stroke-[2.5] text-[#059856]"></i> 2 jaar garantie op al onze producten</div>
                        <div class="flex items-center gap-3 text-[12px] text-[#354E78]"><i data-lucide="check" class="w-[16px] h-[16px] stroke-[2.5] text-[#059856]"></i> Afhalen in Apeldoorn mogelijk</div>
                    </div>

                    <div class="h-px bg-[#DEE5EE] my-7"></div>

                    <div class="grid grid-cols-5 gap-2">
                        <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[10px] font-black text-[#D50067]">iDEAL</span></div>
                        <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[8px] font-black text-[#163A77]">Bancontact</span></div>
                        <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[11px] font-black italic text-[#0070BA]">PayPal</span></div>
                        <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[12px] font-black italic text-[#17357A]">VISA</span></div>
                        <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><div class="relative w-[31px] h-[18px]"><span class="absolute left-0 top-0 w-[18px] h-[18px] rounded-full bg-[#EB001B]"></span><span class="absolute right-0 top-0 w-[18px] h-[18px] rounded-full bg-[#F79E1B] opacity-90"></span></div></div>
                    </div>
                </aside>
            </div>
            @endif

            {{-- TRUST BAR --}}
            <div class="mt-8 sm:mt-10 rounded-[10px] bg-gradient-to-r from-[#F2F6FF] via-[#F8FAFF] to-[#F2F6FF] px-4 sm:px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 sm:gap-7">
                    <div class="flex items-center gap-4">
                        <div class="w-[50px] h-[50px] rounded-full bg-[#E2EBFF] flex items-center justify-center shrink-0"><i data-lucide="truck" class="w-[24px] h-[24px] text-[#0759F5]"></i></div>
                        <div><h4 class="text-[13px] font-bold text-[#08193D]">Gratis verzending</h4><p class="text-[12px] text-[#41577D] mt-1">vanaf €75</p></div>
                    </div>
                    <div class="flex items-center gap-4 xl:border-l xl:border-[#DEE5EF] xl:pl-8">
                        <div class="w-[50px] h-[50px] rounded-full bg-[#E2EBFF] flex items-center justify-center shrink-0"><i data-lucide="store" class="w-[24px] h-[24px] text-[#0759F5]"></i></div>
                        <div><h4 class="text-[13px] font-bold text-[#08193D]">Afhalen in Apeldoorn</h4><p class="text-[12px] text-[#41577D] mt-1">Binnen 24 uur klaar</p></div>
                    </div>
                    <div class="flex items-center gap-4 xl:border-l xl:border-[#DEE5EF] xl:pl-8">
                        <div class="w-[50px] h-[50px] rounded-full bg-[#E2EBFF] flex items-center justify-center shrink-0"><i data-lucide="shield-check" class="w-[24px] h-[24px] text-[#0759F5]"></i></div>
                        <div><h4 class="text-[13px] font-bold text-[#08193D]">2 jaar garantie</h4><p class="text-[12px] text-[#41577D] mt-1">Op al onze producten</p></div>
                    </div>
                    <div class="flex items-center gap-4 xl:border-l xl:border-[#DEE5EF] xl:pl-8">
                        <div class="w-[50px] h-[50px] rounded-full bg-[#E2EBFF] flex items-center justify-center shrink-0"><i data-lucide="wallet-cards" class="w-[24px] h-[24px] text-[#0759F5]"></i></div>
                        <div><h4 class="text-[13px] font-bold text-[#08193D]">Veilig betalen</h4><p class="text-[12px] text-[#41577D] mt-1">iDEAL, Bancontact, PayPal</p></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Winkelwagen legen — exact copy of dashboard x-admin.modal delete style --}}
    <div id="modal-clearCartModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-clearCartModal-title">
        <div data-modal-overlay class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative z-10 flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-4">
                    <h3 id="modal-clearCartModal-title" class="text-base font-bold text-slate-900">Winkelwagen legen</h3>
                    <button type="button" data-modal-close aria-label="Sluiten" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Weet je zeker dat je de winkelwagen wilt legen?</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Alle producten worden verwijderd. Deze actie kan niet ongedaan worden gemaakt.</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse items-stretch justify-end gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center">
                    <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Annuleren</button>
                    <button type="button" id="clearCartConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] hover:bg-red-700">Ja, legen</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Product verwijderen — same dashboard style, per product --}}
    <div id="modal-removeCartItemModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true" aria-labelledby="modal-removeCartItemModal-title">
        <div data-modal-overlay class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
        <div class="relative z-10 flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-6 py-4">
                    <h3 id="modal-removeCartItemModal-title" class="text-base font-bold text-slate-900">Product verwijderen</h3>
                    <button type="button" data-modal-close aria-label="Sluiten" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>
                </div>
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-red-600"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg></span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Weet je zeker dat je <span id="removeCartItemName" class="font-bold">dit product</span> wilt verwijderen?</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Dit product wordt uit je winkelwagen verwijderd.</p>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col-reverse items-stretch justify-end gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center">
                    <button type="button" data-modal-close class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-5 text-sm font-semibold text-slate-700 hover:bg-slate-100">Annuleren</button>
                    <button type="button" id="removeCartItemConfirmBtn" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-red-600 px-6 text-sm font-semibold text-white shadow-[0_10px_25px_rgba(220,38,38,0.25)] hover:bg-red-700">Ja, verwijderen</button>
                </div>
            </div>
        </div>
    </div>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <div id="cartToast" class="fixed bottom-5 right-5 z-[80] hidden max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg"></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

        function moneyFormat(v) {
            return new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v);
        }
        function showToast(msg, type) {
            const el = document.getElementById('cartToast');
            if(!el) return;
            el.textContent = msg;
            el.className = 'fixed bottom-5 right-5 z-[80] max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg ' + (type === 'error' ? 'bg-red-600 text-white' : 'bg-emerald-600 text-white');
            el.style.display = 'block';
            el.classList.remove('hidden');
            setTimeout(() => el.style.display = 'none', 3500);
        }
        window.toast = showToast;

        function updateOverview(totals) {
            document.getElementById('summaryProductLabel').textContent = `Subtotaal (${totals.count} ${totals.count === 1 ? 'product' : 'producten'})`;
            document.getElementById('summarySubtotal').textContent = moneyFormat(totals.subtotal);
            document.getElementById('cartCountBadge').textContent = totals.count;
            document.querySelectorAll('[data-cart-count]').forEach(el => el.textContent = totals.count);
            document.getElementById('summaryTotal').textContent = moneyFormat(totals.subtotal);

            if (window.lucide) lucide.createIcons();

            // If cart empty, reload to show empty state
            if (totals.count === 0) {
                setTimeout(() => location.reload(), 400);
            }
        }

        async function changeCartQuantity(id, delta) {
            const el = document.getElementById(`quantity-${id}`);
            if(!el) return;
            let qty = parseInt(el.textContent, 10) + delta;
            if (qty < 1) qty = 1;

            // Optimistic
            el.textContent = qty;

            try {
                const res = await fetch(`/cart/items/${id}`, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ quantity: qty })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Fout');
                document.getElementById(`subtotal-${id}`).textContent = moneyFormat(data.item_subtotal);
                updateOverview(data.totals);
            } catch(e) {
                showToast(e.message, 'error');
                // Revert on error - reload
                location.reload();
            }
        }

        let pendingRemoveId = null;
        function confirmRemoveCartProduct(id, btn){
            pendingRemoveId = id;
            const title = btn?.getAttribute('data-product-title') || 'dit product';
            document.getElementById('removeCartItemName').textContent = title;
            if(window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.open('removeCartItemModal');
            else doRemoveCartProduct();
        }
        async function doRemoveCartProduct(){
            const id = pendingRemoveId;
            if(!id) return;
            if(window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.close('removeCartItemModal');
            const row = document.getElementById(`product-${id}`);
            if(!row) return;
            row.classList.add('removing');
            try {
                const res = await fetch(`/cart/items/${id}`, {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Fout');
                setTimeout(() => {
                    row.remove();
                    updateOverview(data.totals);
                    showToast(data.message);
                }, 220);
            } catch(e) {
                row.classList.remove('removing');
                showToast(e.message, 'error');
            } finally {
                pendingRemoveId = null;
            }
        }
        // keep old direct call for fallback
        async function removeCartProduct(id){ confirmRemoveCartProduct(id); }
        document.getElementById('removeCartItemConfirmBtn')?.addEventListener('click', doRemoveCartProduct);

        function doClearCart(){
            if(window.SlimmePC && window.SlimmePC.modal) window.SlimmePC.modal.close('clearCartModal');
            fetch('/cart', {
                    method: 'DELETE',
                    credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                }).then(r=>r.json().then(d=>({ok:r.ok,d}))).then(({ok,d})=>{
                if(!ok) throw new Error(d.message||'Fout');
                document.querySelectorAll('.product-row').forEach(el => el.classList.add('removing'));
                setTimeout(() => {
                    document.querySelectorAll('.product-row').forEach(el => el.remove());
                    updateOverview(d.totals);
                    showToast(d.message);
                }, 220);
            }).catch(e=>showToast(e.message,'error'));
        }
        function clearCart() {
            if(window.SlimmePC && window.SlimmePC.modal){
                window.SlimmePC.modal.open('clearCartModal');
            } else {
                if(!confirm('Weet je zeker dat je de winkelwagen wilt legen?')) return;
                doClearCart();
            }
        }
        document.getElementById('clearCartConfirmBtn')?.addEventListener('click', doClearCart);
        // === Dynamic injection        });

        // === Dynamic injection for upsell / any add from cart page (no refresh) ===
        function buildCartRow(item, product){
            const placeholder = '{{ $placeholderSrc }}';
            let img = product.image || placeholder;
            if(!img || img === '') img = placeholder;
            else if(img.startsWith('http')) { /* keep */ }
            else if(img.startsWith('assets/')) img = '/' + img;
            else if(img.startsWith('storage/')) img = '/' + img;
            else if(img.startsWith('/')) { /* keep */ }
            else img = '/storage/' + img.replace(/^\/+/, '');

            // Fallback placeholder handling is done via onerror in HTML
            const specs = product.brand ? product.brand : '';
            const price = moneyFormat(parseFloat(item.price_snapshot));
            const subtotal = moneyFormat(parseFloat(item.price_snapshot) * parseInt(item.quantity));

            return `
            <div id="product-${item.id}" class="product-row" data-price="${item.price_snapshot}">
                <div class="grid grid-cols-2 md:grid-cols-[minmax(0,1fr)_110px_150px_125px_48px] items-center gap-3 md:gap-2 px-4 sm:px-5 md:px-6 py-4 sm:py-5 border-b border-[#DFE6EF] last:border-0">
                    <div class="col-span-2 md:col-span-1 flex items-center gap-3 sm:gap-5 min-w-0">
                        <div class="w-[84px] h-[64px] sm:w-[96px] sm:h-[76px] md:w-[108px] md:h-[84px] rounded-[6px] bg-[#F3F5F8] overflow-hidden flex items-center justify-center shrink-0">
                            <a href="/webshop/${product.category ? product.category.slug : 'laptops'}/${product.slug}">
                                <img src="${img}" alt="${product.title}" class="w-full h-full object-cover" onerror="this.src='${placeholder}'">
                            </a>
                        </div>
                        <div class="min-w-0">
                            <a href="/webshop/${product.category ? product.category.slug : 'laptops'}/${product.slug}">
                                <h3 class="text-[14px] leading-5 font-semibold text-[#081A40] hover:text-[#0759F5] transition line-clamp-2">${product.title}</h3>
                            </a>
                            ${specs ? `<p class="mt-1 text-[13px] leading-5 text-[#3D527D] line-clamp-1">${specs}</p>` : ''}
                            <div class="flex items-center gap-2 mt-1.5 text-[12px] text-[#087F49]">
                                <span class="w-[8px] h-[8px] rounded-full bg-[#08AF5A]"></span>
                                Op voorraad
                            </div>
                        </div>
                    </div>
                    <div class="col-span-1">
                        <span class="md:hidden block mb-1 text-[11px] text-[#7685A4]">Prijs</span>
                        <span class="text-[15px] sm:text-[17px] font-bold whitespace-nowrap text-[#07163B]">${price}</span>
                    </div>
                    <div class="col-span-1 flex justify-end md:justify-center">
                        <div class="inline-flex h-[40px] sm:h-[43px] rounded-[6px] border border-[#D6DFEB] overflow-hidden">
                            <button onclick="changeCartQuantity(${item.id}, -1)" class="quantity-button w-[38px] sm:w-[43px] flex items-center justify-center hover:bg-[#F5F7FA] transition">
                                <i data-lucide="minus" class="w-[15px] h-[15px] sm:w-[16px] sm:h-[16px]"></i>
                            </button>
                            <div id="quantity-${item.id}" class="w-[38px] sm:w-[43px] border-x border-[#DFE5ED] flex items-center justify-center font-semibold text-[13px] sm:text-[14px]">${item.quantity}</div>
                            <button onclick="changeCartQuantity(${item.id}, 1)" class="quantity-button w-[38px] sm:w-[43px] flex items-center justify-center hover:bg-[#F5F7FA] transition">
                                <i data-lucide="plus" class="w-[15px] h-[15px] sm:w-[16px] sm:h-[16px]"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-span-1 md:text-center">
                        <span class="md:hidden block mb-1 text-[11px] text-[#7685A4]">Subtotaal</span>
                        <span id="subtotal-${item.id}" class="text-[15px] sm:text-[17px] font-bold whitespace-nowrap text-[#07163B]">${subtotal}</span>
                    </div>
                    <div class="col-span-1 flex justify-end md:justify-center">
                        <button onclick="confirmRemoveCartProduct(${item.id}, this)" data-product-title="${product.title.replace(/"/g, '&quot;')}" aria-label="Product verwijderen" class="w-[38px] h-[38px] sm:w-[42px] sm:h-[42px] shrink-0 rounded-[7px] border border-[#D7E0EC] flex items-center justify-center text-[#29446F] bg-white hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition">
                            <i data-lucide="trash-2" class="w-[16px] h-[16px] sm:w-[18px] sm:h-[18px]"></i>
                        </button>
                    </div>
                </div>
            </div>`;
        }

        document.addEventListener('cart:itemAdded', function(e){
            const data = e.detail;
            if(!data || !data.item || !data.totals) return;
            // Update overview totals immediately
            try { updateOverview(data.totals); } catch(err) {}
            // If cart was empty (no container), reload to show full layout
            const container = document.getElementById('cartProducts');
            if(!container){
                setTimeout(()=> location.reload(), 400);
                return;
            }
            const existing = document.getElementById('product-' + data.item.id);
            if(existing){
                const qtyEl = document.getElementById('quantity-' + data.item.id);
                if(qtyEl) qtyEl.textContent = data.item.quantity;
                const subEl = document.getElementById('subtotal-' + data.item.id);
                if(subEl) subEl.textContent = moneyFormat(parseFloat(data.item.price_snapshot) * parseInt(data.item.quantity));
                if(window.lucide) lucide.createIcons();
                return;
            }
            // New item — inject row
            const html = buildCartRow(data.item, data.item.product);
            container.insertAdjacentHTML('beforeend', html);
            if(window.lucide) lucide.createIcons();
        });
    </script>
@endsection
