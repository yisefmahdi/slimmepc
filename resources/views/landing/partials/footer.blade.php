<footer class="
        relative overflow-hidden
        border-t border-blue-300
        bg-gradient-to-b
        from-[#edf5ff]
        via-[#83b9eb]
        to-[#d7e8ff]
    ">
    <div class="
            mx-auto
            max-w-[1500px]
            px-4 py-14
            sm:px-6
            lg:px-8
            lg:py-16
        ">

        <div class="
                grid gap-10
                sm:grid-cols-2
                lg:grid-cols-[1.3fr_1fr_1fr_1fr_1.2fr]
            ">

            <div class="
                    pointer-events-none absolute
                    -left-40 top-20
                    h-[500px] w-[500px]
                    rounded-full
                    bg-blue-400/15
                    blur-[140px]
                "></div>

            <div class="
                    pointer-events-none absolute
                    -right-40 bottom-20
                    h-[500px] w-[500px]
                    rounded-full
                    bg-cyan-300/15
                    blur-[150px]
                "></div>

            <!-- BRAND -->
            <div>
                <div class="flex items-center gap-3">
                    @if (($c['header']['logo_image'] ?? null))
                        <img src="{{ asset($c['header']['logo_image']) }}" alt="{{ $c['header']['logo_text'] ?? '' }}"
                             class="h-14 w-14 rounded-2xl bg-white/90 object-contain p-1.5 shadow-lg shadow-blue-500/20" loading="lazy" decoding="async">
                    @else
                        <div class="
                                flex h-14 w-14
                                items-center justify-center
                                rounded-2xl
                                bg-brand-gradient-br
                                text-white
                                shadow-lg shadow-blue-500/20
                            ">
                            <span class="text-2xl font-black">{{ $c['footer']['brand_badge_letter'] ?? 'S' }}</span>
                        </div>
                    @endif

                    <div>
                        <h3 class="
                                text-2xl font-extrabold
                                text-brand-heading
                            ">
                            {{ $c['footer']['brand_name'] ?? '' }}
                        </h3>

                        <p class="
                                mt-1 text-xs
                                font-semibold
                                text-brand-primary-dark
                            ">
                            {{ $c['footer']['brand_tagline'] ?? '' }}
                        </p>
                    </div>
                </div>

                <p class="
                        mt-6 max-w-[300px]
                        text-sm leading-7
                        text-slate-600
                    ">
                    {{ $c['footer']['brand_about'] ?? '' }}
                </p>

                <div class="mt-6 flex items-center gap-3">
                    @foreach ($c['footer']['social'] ?? [] as $social)
                        <a href="{{ $social['url'] ?? '#' }}" class="footer-social">
                            <i data-lucide="{{ $social['icon'] ?? 'circle' }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- DIENSTEN -->
            <div>
                <h4 class="footer-title">
                    Diensten
                </h4>

                <ul class="footer-links">
                    @foreach ($c['footer']['services_col'] ?? [] as $link)
                        <li>
                            <a href="{{ $link['url'] ?? '#' }}">
                                {{ $link['label'] ?? '' }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- WEBSHOP -->
            <div>
                <h4 class="footer-title">
                    Webshop
                </h4>

                <ul class="footer-links">
                    @foreach ($c['footer']['shop_col'] ?? [] as $link)
                        <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- OVER ONS -->
            <div>
                <h4 class="footer-title">
                    Over ons
                </h4>

                <ul class="footer-links">
                    @foreach ($c['footer']['about_col'] ?? [] as $link)
                        <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
                    @endforeach
                </ul>
            </div>

            <!-- CONTACT -->
            <div>
                <h4 class="footer-title">
                    Contact
                </h4>

                <div class="
                        rounded-[24px]
                        border border-blue-100
                        bg-white/80
                        p-5
                        shadow-sm
                        backdrop-blur-sm
                    ">
                    @foreach ($c['footer']['contact'] ?? [] as $row)
                        <div class="footer-contact-row">
                            <i data-lucide="{{ $row['icon'] ?? 'circle' }}"></i>

                            <div>
                                <strong>{{ $row['label'] ?? '' }}</strong>
                                <span>{!! nl2br(e($row['value'] ?? '')) !!}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TRUST BAR -->
        <div class="
                mt-14 grid overflow-hidden
                rounded-[24px]
                border border-blue-100
                bg-white/80
                shadow-sm
                sm:grid-cols-2
                lg:grid-cols-4
            ">
            @foreach ($c['footer']['trust'] ?? [] as $item)
                <div class="footer-trust">
                    <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}"></i>

                    <div>
                        <strong>{{ $item['title'] ?? '' }}</strong>
                        <span>{{ $item['subtitle'] ?? '' }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- BOTTOM BAR -->
    <div class="
            relative overflow-hidden
            bg-brand-gradient-btn
        ">
        <!-- subtle wave -->
        <svg class="
                pointer-events-none absolute
                inset-0 h-full w-full
                opacity-15
            " viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path d="M0,80 C260,10 430,110 700,55 C970,5 1170,100 1440,40" fill="none" stroke="white"
                stroke-width="2" />
        </svg>

        <div class="
                relative mx-auto
                flex max-w-[1500px]
                flex-col gap-5
                px-4 py-7
                text-sm text-white/90
                sm:px-6
                lg:flex-row
                lg:items-center
                lg:justify-between
                lg:px-8
            ">
            <div>
                {{ $c['footer']['copyright'] ?? '' }}
            </div>

            <div class="flex flex-wrap items-center gap-3">
                @foreach ($c['footer']['payments'] ?? [] as $payment)
                    <div class="payment-badge">{{ $payment['label'] ?? '' }}</div>
                @endforeach
            </div>
        </div>
    </div>
</footer>
