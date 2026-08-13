<section id="processHero" class="
        relative isolate overflow-hidden
        bg-gradient-to-br
        from-white via-blue-50/70 to-blue-100/60
        text-slate-900
    ">
    <!-- Soft background glows -->
    <div class="
            pointer-events-none absolute
            -left-32 top-16
            h-[420px] w-[420px]
            rounded-full bg-blue-200/25
            blur-[120px]
        "></div>

    <div class="
            pointer-events-none absolute
            -right-36 top-10
            h-[520px] w-[520px]
            rounded-full bg-blue-400/20
            blur-[130px]
        "></div>

    <!-- Subtle dotted background -->
    <div class="
            pointer-events-none absolute
            inset-0 opacity-[0.12]
        " style="
            background-image:
                radial-gradient(rgba(37, 99, 235, 0.42) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image:
                linear-gradient(
                    to bottom,
                    transparent 4%,
                    black 22%,
                    black 78%,
                    transparent 100%
                );
        "></div>

    <div class="
            relative mx-auto
            grid max-w-[1440px]
            items-center gap-14
            px-4 pb-16 pt-10
            sm:px-6 sm:pt-12
            lg:grid-cols-[0.88fr_1.12fr]
            lg:px-8 lg:pb-14 lg:pt-8
            xl:gap-20
        ">
        <!-- LEFT CONTENT -->
        <div class="relative z-20">
            <!-- Badge -->
            <div class="
                    process-reveal
                    flex max-w-full flex-wrap
                    items-center justify-center gap-2
                    rounded-full
                    border border-blue-200
                    bg-white/85
                    px-4 py-2
                    text-center text-xs font-bold
                    text-blue-950
                    shadow-sm
                    backdrop-blur-md
                    sm:inline-flex sm:justify-start sm:text-left sm:text-sm
                ">
                <span class="
                        h-2 w-2 rounded-full
                        bg-brand-accent
                        shadow-[0_0_12px_rgba(163,230,53,.9)]
                    "></span>

                {{ $c['hero']['badge'] ?? '' }}
            </div>

            <!-- Heading -->
            <h1 class="
                    process-reveal process-delay-1
                    mt-7 max-w-[650px]
                    text-[clamp(1.7rem,8vw,3rem)]
                    font-extrabold
                    leading-[1.12]
                    tracking-tight
                    text-brand-heading
                    sm:text-[46px]
                    lg:text-[52px]
                    lg:leading-[1.08]
                ">
                {{ $c['hero']['title_line1'] ?? '' }}

                <span class="block">
                    {{ $c['hero']['title_line2'] ?? '' }}
                </span>

                <span class="block gradient-text">
                    {{ $c['hero']['title_gradient'] ?? '' }}
                </span>
            </h1>

            <!-- Description -->
            <p class="
                    process-reveal process-delay-2
                    mt-6 max-w-xl
                    text-base leading-7
                    text-slate-600
                    sm:text-lg
                ">
                {{ $c['hero']['description'] ?? '' }}
            </p>

            <!-- Buttons -->
            <div class="
                    process-reveal process-delay-3
                    mt-8 flex flex-col gap-3
                    sm:flex-row
                ">
                @foreach ($c['hero']['buttons'] ?? [] as $btn)
                    @if (($btn['variant'] ?? 'primary') === 'outline')
                        <a href="{{ $btn['url'] ?? '#' }}" class="
                            group inline-flex min-h-[54px]
                            items-center justify-center gap-3
                            rounded-2xl
                            border border-slate-300
                            bg-white/85 px-6
                            text-sm font-bold
                            text-slate-900
                            shadow-sm backdrop-blur-md
                            transition duration-300
                            hover:-translate-y-1
                            hover:border-blue-300
                            hover:bg-white
                            hover:shadow-md
                        ">
                            <i data-lucide="{{ $btn['icon'] ?? 'calendar-check' }}" class="h-5 w-5"></i>

                            {{ $btn['label'] ?? '' }}

                            <i data-lucide="arrow-right" class="
                                    h-4 w-4
                                    transition-transform
                                    group-hover:translate-x-1
                                "></i>
                        </a>
                    @else
                        <a href="{{ $btn['url'] ?? '#' }}" class="
                            group inline-flex min-h-[54px]
                            items-center justify-center gap-3
                            rounded-2xl bg-brand-btn
                            px-6 text-sm font-extrabold
                            text-white
                            shadow-[0_16px_35px_rgba(37,99,235,.22)]
                            transition duration-300
                            hover:-translate-y-1
                            hover:shadow-[0_20px_45px_rgba(37,99,235,.32)]
                        ">
                            <i data-lucide="{{ $btn['icon'] ?? 'wrench' }}" class="h-5 w-5"></i>

                            {{ $btn['label'] ?? '' }}

                            <i data-lucide="arrow-right" class="
                                h-4 w-4
                                transition-transform
                                group-hover:translate-x-1
                            "></i>
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- Trust points -->
            <div class="
                    process-reveal process-delay-4
                    mt-9 flex flex-wrap
                    gap-x-7 gap-y-4
                ">
                @foreach ($c['hero']['trust'] ?? [] as $point)
                    <div class="flex items-center gap-3">
                        <span class="
                            flex h-10 w-10
                            items-center justify-center
                            rounded-xl bg-brand-primary/10
                            text-brand-primary
                        ">
                            <i data-lucide="{{ $point['icon'] ?? 'shield-check' }}" class="h-5 w-5"></i>
                        </span>

                        <div>
                            <strong class="block text-xs text-slate-900">
                                {{ $point['title'] ?? '' }}
                            </strong>

                            <span class="text-[11px] text-slate-500">
                                {{ $point['subtitle'] ?? '' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT PROCESS VISUAL -->
        <div id="repairProcess" class="
                process-reveal process-delay-2
                relative mx-auto w-full
                max-w-[720px]
            ">
            <!-- Desktop circular process -->
            <div class="
                    relative hidden
                    aspect-square w-full
                    min-h-[580px]
                    lg:block
                ">
                <!-- Main blue blob -->
                <div class="
                        process-blob absolute
                        left-1/2 top-1/2
                        h-[560px] w-[560px]
                        -translate-x-1/2
                        -translate-y-1/2
                        rounded-full
                        bg-gradient-to-br
                        from-blue-200
                        via-blue-400
                        to-blue-600
                        opacity-90
                        shadow-[0_35px_90px_rgba(37,99,235,.28)]
                    "></div>

                <!-- Inner glow -->
                <div class="
                        pointer-events-none absolute
                        left-1/2 top-1/2
                        h-[360px] w-[360px]
                        -translate-x-1/2
                        -translate-y-1/2
                        rounded-full bg-white/25
                        blur-[80px]
                    "></div>

                <!-- Orbit SVG -->
                <svg class="
                        process-orbit absolute
                        inset-0 h-full w-full
                    " viewBox="0 0 720 720" aria-hidden="true">
                    <circle cx="360" cy="360" r="290" fill="none" stroke="rgba(255,255,255,.92)" stroke-width="4"
                        stroke-linecap="round" stroke-dasharray="16 11"></circle>

                    <path d="M351 110 L369 110 L360 128 Z" fill="white"></path>
                    <path d="M610 351 L610 369 L592 360 Z" fill="white"></path>
                    <path d="M351 610 L369 610 L360 592 Z" fill="white"></path>
                    <path d="M110 351 L110 369 L128 360 Z" fill="white"></path>
                </svg>

                <!-- Moving light -->
                <div class="process-light-orbit">
                    <span class="process-moving-light"></span>
                </div>

                <!-- Laptop -->
                <div class="
                        process-laptop absolute
                        left-1/2 top-1/2
                        z-20 w-[58%]
                        -translate-x-1/2
                        -translate-y-[42%]
                    ">
                    <img src="{{ asset($c['hero']['hero_image'] ?? '') }}" alt="{{ $c['hero']['hero_image_alt'] ?? '' }}"
                        fetchpriority="high" decoding="async"
                        class="
                            h-auto
                            w-[111%]
                            max-w-none
                            object-contain
                            drop-shadow-[0_35px_35px_rgba(15,23,42,.32)]
                        ">
                </div>

                @php
                    $stagePositions = [
                        0 => 'left-1/2 top-8 -translate-x-1/2',
                        1 => 'right-0 top-1/2 -translate-y-1/2',
                        2 => 'bottom-0 left-1/2 -translate-x-1/2',
                        3 => 'left-0 top-1/2 -translate-y-1/2',
                    ];
                @endphp

                @foreach ($c['process']['steps'] ?? [] as $i => $step)
                    <article class="
                            process-stage process-stage-{{ $i + 1 }}
                            absolute {{ $stagePositions[$i] ?? '' }}
                            z-30 w-[145px]
                            rounded-[999px]
                            border border-white/80
                            bg-white/95
                            px-4 pb-5 pt-6
                            text-center
                            shadow-[0_18px_45px_rgba(15,23,42,.12)]
                            backdrop-blur-xl
                        " data-step="{{ $i + 1 }}">
                        <span class="
                            absolute left-1/2 top-0
                            flex h-7 w-7
                            -translate-x-1/2
                            -translate-y-1/2
                            items-center justify-center
                            rounded-full bg-brand-primary
                            text-[11px] font-extrabold text-white
                        ">
                            {{ $step['number'] ?? $i + 1 }}
                        </span>

                        <span class="
                            mx-auto flex h-10 w-10
                            items-center justify-center
                            rounded-full {{ ($i + 1) === 4 ? 'bg-lime-50 text-lime-600' : 'bg-brand-primary/10 text-brand-primary' }}
                        ">
                            <i data-lucide="{{ $step['icon'] ?? 'circle' }}" class="h-5 w-5"></i>
                        </span>

                        <h3 class="mt-2 text-xs font-extrabold text-slate-950">
                            {{ $step['title'] ?? '' }}
                        </h3>

                        <p class="mt-1 text-[11px] leading-4 text-slate-500">
                            {{ $step['description'] ?? '' }}
                        </p>
                    </article>
                @endforeach
            </div>

            <!-- Mobile and tablet process -->
            <div class="lg:hidden">
                <!-- Laptop card -->
                <div class="
                        relative mx-auto
                        flex min-h-[320px]
                        max-w-[560px]
                        items-center justify-center
                        overflow-hidden
                        rounded-[34px]
                        bg-gradient-to-br
                        from-blue-200
                        via-blue-400
                        to-blue-600
                        p-6
                        shadow-[0_25px_60px_rgba(37,99,235,.25)]
                    ">
                    <div class="
                            absolute inset-8
                            rounded-full bg-white/20
                            blur-[60px]
                        "></div>

                    <img src="{{ asset($c['hero']['hero_image_mobile'] ?? $c['hero']['hero_image'] ?? '') }}"
                        alt="{{ $c['hero']['hero_image_alt'] ?? '' }}" decoding="async" class="
                            process-mobile-laptop
                            relative z-10
                            w-[90%] max-w-[430px]
                            object-contain
                            drop-shadow-[0_28px_28px_rgba(15,23,42,.28)]
                        ">
                </div>

                <!-- Steps -->
                <div class="
                        mt-6 grid gap-3
                        sm:grid-cols-2
                    ">
                    @foreach ($c['process']['steps'] ?? [] as $i => $step)
                        <article class="
                            process-mobile-stage
                            flex items-start gap-4
                            rounded-2xl
                            border {{ ($i + 1) === 4 ? 'border-lime-100' : 'border-blue-100' }}
                            bg-white/90 p-4
                            shadow-sm backdrop-blur
                        ">
                            <span class="
                                flex h-11 w-11 shrink-0
                                items-center justify-center
                                rounded-xl {{ ($i + 1) === 4 ? 'bg-brand-accent' : 'bg-brand-primary' }}
                                font-extrabold text-white
                            ">
                                {{ $step['number'] ?? $i + 1 }}
                            </span>

                            <div>
                                <div class="flex items-center gap-2">
                                    <i data-lucide="{{ $step['icon'] ?? 'circle' }}" class="h-5 w-5 {{ ($i + 1) === 4 ? 'text-lime-600' : 'text-brand-primary' }}"></i>

                                    <h3 class="font-extrabold text-slate-950">
                                        {{ $step['title'] ?? '' }}
                                    </h3>
                                </div>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $step['description'] ?? '' }}
                                </p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom wave -->
    <div class="
            pointer-events-none absolute
            bottom-[-1px] left-0 right-0
            h-14 overflow-hidden
            sm:h-20
        ">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none" class="h-full w-full" aria-hidden="true">
            <path fill="#ffffff" d="
                M0,74
                C260,24 480,95 730,67
                C1000,36 1200,25 1440,66
                L1440,120
                L0,120
                Z
            "></path>
        </svg>
    </div>
</section>

