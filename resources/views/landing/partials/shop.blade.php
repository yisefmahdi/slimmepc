<section id="webshop-showcase" class="
        relative overflow-hidden
        bg-gradient-to-b
        from-white via-blue-50/50 to-white
        py-20 sm:py-24 lg:py-28
    ">
    <!-- background glow -->
    <div class="
            pointer-events-none absolute
            -left-40 top-16
            h-[500px] w-[500px]
            rounded-full bg-blue-200/25
            blur-[150px]
        "></div>

    <div class="
            pointer-events-none absolute
            -right-40 bottom-0
            h-[500px] w-[500px]
            rounded-full bg-cyan-200/20
            blur-[160px]
        "></div>

    <div class="
            relative mx-auto
            max-w-[1500px]
            px-4 sm:px-6 lg:px-8
        ">
        <div class="
                grid gap-12
                lg:grid-cols-[320px_1fr]
                xl:grid-cols-[350px_1fr]
                lg:items-center
            ">
            <!-- LEFT CONTENT -->
            <div>
                <div class="
                        flex max-w-full flex-wrap
                        items-center justify-center gap-2
                        rounded-full
                        border border-blue-200
                        bg-blue-50
                        px-4 py-2
                        text-center text-xs font-extrabold
                        uppercase tracking-[0.15em]
                        text-brand-primary-dark
                        sm:inline-flex sm:justify-start sm:text-left
                    ">
                    <i data-lucide="shopping-cart" class="h-4 w-4"></i>
                    {{ $c['shop']['badge'] ?? '' }}
                </div>

                <h2 class="
                        mt-6
                        text-4xl font-extrabold
                        tracking-tight text-brand-heading
                        sm:text-5xl
                    ">
                    {{ $c['shop']['title_prefix'] ?? '' }}

                    <span class="block gradient-text">
                        {{ $c['shop']['title_highlight'] ?? '' }}
                    </span>
                </h2>

                <p class="
                        mt-5
                        text-base leading-8
                        text-slate-600
                    ">
                    {{ $c['shop']['description'] ?? '' }}
                </p>

                <!-- benefits -->
                <div class="mt-8 space-y-5">
                    @foreach ($c['shop']['benefits'] ?? [] as $benefit)
                        <div class="shop-benefit">
                            <div class="shop-benefit-icon">
                                <i data-lucide="{{ $benefit['icon'] ?? 'shield-check' }}"></i>
                            </div>

                            <div>
                                <strong>{{ $benefit['title'] ?? '' }}</strong>
                                <span>{{ $benefit['subtitle'] ?? '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-9 flex flex-col items-stretch gap-4 sm:flex-row sm:items-center">
                    <a href="{{ $c['shop']['cta_url'] ?? '/webshop' }}" class="
                            inline-flex
                            items-center justify-center gap-3
                            rounded-xl
                            bg-brand-btn
                            px-6 py-4
                            font-bold text-white
                            shadow-lg shadow-blue-600/20
                            transition
                            hover:-translate-y-1
                            sm:justify-start
                        ">
                        {{ $c['shop']['cta_label'] ?? 'Bekijk All!' }}

                        <i data-lucide="arrow-right" class="h-5 w-5"></i>
                    </a>

                    <div class="shop-note">
                        <i data-lucide="corner-down-left"></i>

                        <span>
                            {{ $c['shop']['note_title'] ?? '' }}<br>
                            {{ $c['shop']['note_subtitle'] ?? '' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- PRODUCTS -->
            <div class="min-w-0">
                <!-- PRODUCTS GRID -->
                <div class="shop-carousel">
                    <div class="shop-products-viewport">
                        <div class="shop-products-track" id="shopProductsTrack">
                            @foreach ($c['shop']['products'] ?? [] as $product)
                                <a href="{{ $product['link'] ?? '#' }}" class="product-card group">
                                    <div class="product-image-box">
                                        @if (!empty($product['badge']))
                                            <span class="product-badge badge-{{ $product['badge_color'] ?? 'blue' }}">
                                                {{ $product['badge'] }}
                                            </span>
                                        @endif

                                        <img src="{{ asset('assets/img/landing/' . basename($product['image'] ?? '')) }}"
                                            alt="{{ $product['title'] ?? '' }}" class="product-image" loading="lazy" decoding="async">
                                    </div>

                                    <div class="product-body">
                                        <h3>{{ $product['title'] ?? '' }}</h3>

                                        <p>
                                            {{ $product['specs'] ?? '' }}
                                        </p>

                                        <div class="product-price">
                                            {{ $product['price'] ?? '' }}
                                        </div>

                                        <div class="product-footer">
                                            <span class="product-stock">
                                                <i data-lucide="check-circle"></i>
                                                {{ !empty($product['in_stock']) ? 'Op voorraad' : 'Niet op voorraad' }}
                                            </span>

                                            <span class="product-arrow">
                                                <i data-lucide="arrow-right"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- SLIDER NAVIGATION -->
                <div class="shop-navigation">
                    <div></div>

                    <!-- dots (generated by JS) -->
                    <div class="shop-dots" id="shopDots"></div>

                    <div class="shop-arrows">
                        <button type="button" class="shop-arrow-btn" id="shopPrev" aria-label="Vorige producten">
                            <i data-lucide="chevron-left"></i>
                        </button>

                        <button type="button" class="shop-arrow-btn" id="shopNext" aria-label="Volgende producten">
                            <i data-lucide="chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTTOM TRUST BAR -->
        <div class="
                mt-16 grid overflow-hidden
                rounded-[26px]
                border border-blue-100
                bg-white/80
                shadow-[0_20px_60px_rgba(15,23,42,.07)]
                backdrop-blur-xl
                sm:grid-cols-2
                xl:grid-cols-4
            ">
            @foreach ($c['shop']['trust'] ?? [] as $item)
                <div class="shop-trust">
                    <div class="shop-trust-icon">
                        <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}"></i>
                    </div>

                    <div>
                        <strong>{{ $item['title'] ?? '' }}</strong>
                        <span>{{ $item['subtitle'] ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

