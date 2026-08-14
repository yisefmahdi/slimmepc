<section id="services" class="
        relative isolate overflow-hidden
        bg-gradient-to-b
        from-white via-blue-50/70
        to-blue-100/60
        py-20
        sm:py-24
        lg:py-28
    ">
    <div class="
            pointer-events-none absolute
            top-[-250px]
            left-1/2
            h-[600px]
            w-[1200px]
            -translate-x-1/2
            rounded-full
            bg-blue-500/25
            blur-[180px]
        ">
    </div>

    <div class="
            pointer-events-none absolute
            -left-40 top-16
            h-[520px] w-[520px]
            rounded-full bg-blue-200/30
            blur-[150px]
        "></div>

    <div class="
            pointer-events-none absolute
            -right-40 top-28
            h-[560px] w-[560px]
            rounded-full bg-cyan-200/25
            blur-[150px]
        "></div>

    <!-- Dotted decoration -->
    <div class="
            pointer-events-none absolute
            inset-0 opacity-[0.09]
        " style="
            background-image:
                radial-gradient(rgba(37, 99, 235, 0.55) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image:
                linear-gradient(
                    to bottom,
                    transparent 0%,
                    black 16%,
                    black 85%,
                    transparent 100%
                );
        "></div>

    <div class="
            relative mx-auto
            max-w-[1500px]
            px-4 sm:px-6 lg:px-8
        ">
        <!-- Heading -->
        <div class="mx-auto max-w-3xl text-center">
            <div class="
                    inline-flex items-center gap-2
                    rounded-full
                    border border-blue-200
                    bg-blue-50/90
                    px-4 py-2
                    text-xs font-extrabold
                    uppercase tracking-[0.14em]
                    text-brand-primary-dark
                    shadow-sm
                ">
                <i data-lucide="wrench" class="h-4 w-4"></i>

                {{ $c['services']['badge'] ?? '' }}
            </div>

            <h2 class="
                    mt-5 text-3xl
                    font-extrabold
                    tracking-tight
                    text-brand-heading
                    sm:text-4xl
                    lg:text-5xl
                ">
                {{ $c['services']['title_prefix'] ?? '' }}

                <span class="gradient-text">
                    {{ $c['services']['title_highlight'] ?? '' }}
                </span>

                {{ $c['services']['title_suffix'] ?? '' }}
            </h2>

            <p class="
                    mx-auto mt-5
                    max-w-2xl
                    text-base leading-7
                    text-slate-600
                    sm:text-lg
                ">
                {{ $c['services']['description'] ?? '' }}
            </p>

            <span class="
                    mx-auto mt-6 block
                    h-1 w-16
                    rounded-full
                    bg-brand-gradient
                "></span>
        </div>

        <!-- Services -->
        <div class="
                mt-14 grid
                gap-x-8 gap-y-14
                sm:grid-cols-2
                xl:grid-cols-4
            ">
            @foreach ($c['services']['services'] ?? [] as $service)
                @continue(!empty($service['hidden']))
                <a href="{{ $service['link'] ?? '#' }}" class="service-showcase group">
                    <div class="service-visual">
                        <div class="service-platform"></div>

                        <img src="{{ asset('assets/img/landing/' . ($service['image'] ?? '')) }}" alt="{{ $service['title'] ?? '' }}"
                            class="service-device" loading="lazy" decoding="async">
                    </div>

                    <div class="service-content">
                        <div class="service-icon">
                            <i data-lucide="{{ $service['icon'] ?? 'wrench' }}" class="h-6 w-6"></i>
                        </div>

                        <h3 class="service-title">
                            {{ $service['title'] ?? '' }}
                        </h3>

                        <p class="service-description">
                            {{ $service['description'] ?? '' }}
                        </p>

                        <span class="service-link">
                            Bekijk service

                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

