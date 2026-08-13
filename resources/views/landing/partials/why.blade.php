<section id="whySlimmePc" class="
        relative isolate overflow-hidden
        bg-white py-20
        sm:py-24
        lg:py-28
    ">
    <!-- Background decoration -->
    <div class="
            pointer-events-none absolute
            left-1/2 top-1/2
            h-[700px] w-[700px]
            -translate-x-1/2 -translate-y-1/2
            rounded-full bg-blue-100/40
            blur-[140px]
        "></div>

    <div class="
            pointer-events-none absolute
            inset-0 opacity-[0.08]
        " style="
            background-image:
                radial-gradient(rgba(37,99,235,.7) 1px, transparent 1px);
            background-size: 30px 30px;
            mask-image:
                linear-gradient(
                    to bottom,
                    transparent,
                    black 18%,
                    black 82%,
                    transparent
                );
        "></div>

    <div class="
            relative mx-auto
            max-w-[1400px]
            px-4 sm:px-6 lg:px-8
        ">
        <!-- Section heading -->
        <div class="mx-auto max-w-3xl text-center">
            <div class="
                    inline-flex items-center gap-2
                    rounded-full
                    border border-blue-100
                    bg-blue-50
                    px-4 py-2
                    text-xs font-extrabold
                    uppercase tracking-[0.14em]
                    text-brand-primary-dark
                ">
                <i data-lucide="badge-check" class="h-4 w-4"></i>

                {{ $c['why']['badge'] ?? '' }}
            </div>

            <h2 class="
                    mt-5 text-3xl
                    font-extrabold
                    tracking-tight
                    text-brand-heading
                    sm:text-4xl
                    lg:text-5xl
                ">
                {{ $c['why']['title_prefix'] ?? '' }}

                <span class="gradient-text">
                    {{ $c['why']['title_highlight'] ?? '' }}
                </span>
            </h2>

            <p class="
                    mx-auto mt-5
                    max-w-2xl
                    text-base leading-7
                    text-slate-600
                    sm:text-lg
                ">
                {{ $c['why']['description'] ?? '' }}
            </p>

            <span class="
                    mx-auto mt-6 block
                    h-1 w-16
                    rounded-full
                    bg-brand-gradient
                "></span>
        </div>

        <!-- DESKTOP BENEFITS DIAGRAM -->
        <div class="
                relative mt-16 hidden
                min-h-[640px]
                lg:block
            ">
            <!-- Central connection lines -->
            <svg class="
                    pointer-events-none
                    absolute inset-0
                    h-full w-full
                " viewBox="0 0 1400 640" preserveAspectRatio="none" aria-hidden="true">
                <path d="M445 130 H520 Q545 130 565 158 L610 218" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>
                <path d="M445 320 H585" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>
                <path d="M445 510 H520 Q545 510 565 482 L610 422" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>
                <path d="M955 130 H880 Q855 130 835 158 L790 218" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>
                <path d="M955 320 H815" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>
                <path d="M955 510 H880 Q855 510 835 482 L790 422" fill="none" stroke="url(#lineGradient)" stroke-width="2"></path>

                <defs>
                    <linearGradient id="lineGradient" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="#2563eb" stop-opacity=".25"></stop>
                        <stop offset="50%" stop-color="#2563eb" stop-opacity=".9"></stop>
                        <stop offset="100%" stop-color="#60a5fa" stop-opacity=".35"></stop>
                    </linearGradient>
                </defs>
            </svg>

            <!-- Connection dots -->
            <span class="why-dot absolute left-[43.3%] top-[33.5%]"></span>
            <span class="why-dot absolute left-[41.2%] top-[49.5%]"></span>
            <span class="why-dot absolute left-[43.3%] top-[65.5%]"></span>
            <span class="why-dot absolute right-[43.3%] top-[33.5%]"></span>
            <span class="why-dot absolute right-[41.2%] top-[49.5%]"></span>
            <span class="why-dot absolute right-[43.3%] top-[65.5%]"></span>

            <!-- Central hub -->
            <div class="
                    why-hub absolute
                    left-1/2 top-1/2
                    z-20 flex
                    h-[330px] w-[330px]
                    -translate-x-1/2
                    -translate-y-1/2
                    items-center justify-center
                    rounded-full
                    border border-blue-200/80
                    bg-white/95
                    shadow-[0_35px_90px_rgba(37,99,235,.18)]
                    backdrop-blur-xl
                ">
                <div class="
                        absolute inset-[-26px]
                        rounded-full
                        border border-dashed
                        border-blue-300/60
                    "></div>

                <div class="
                        absolute inset-[-10px]
                        rounded-full
                        bg-blue-200/25
                        blur-2xl
                    "></div>

                <div class="relative text-center">
                    <div class="
                            mx-auto flex
                            h-20 w-20
                            items-center justify-center
                            rounded-[24px]
                            bg-brand-gradient-br
                            text-white
                            shadow-[0_18px_40px_rgba(37,99,235,.28)]
                        ">
                        <i data-lucide="{{ $c['why']['hub_icon'] ?? 'laptop-minimal-check' }}" class="h-10 w-10"></i>
                    </div>

                    <h3 class="
                            mt-5 text-3xl
                            font-extrabold
                            tracking-tight
                            text-brand-heading
                        ">
                        {{ $c['why']['hub_title'] ?? '' }}
                    </h3>

                    <p class="
                            mt-1 text-sm
                            font-semibold
                            text-slate-600
                        ">
                        {{ $c['why']['hub_subtitle'] ?? '' }}
                    </p>
                </div>
            </div>

            <!-- Benefits (desktop positions) -->
            @php
                $benefitPositions = [
                    0 => 'left-0 top-[35px]',
                    1 => 'left-0 top-[225px]',
                    2 => 'bottom-[35px] left-0',
                    3 => 'right-0 top-[35px]',
                    4 => 'right-0 top-[225px]',
                    5 => 'bottom-[35px] right-0',
                ];
            @endphp

            @foreach ($c['why']['benefits'] ?? [] as $i => $benefit)
                <article class="
                        why-card absolute
                        {{ $benefitPositions[$i] ?? 'left-0 top-0' }}
                        z-20 flex
                        w-[380px]
                        items-start gap-5
                        rounded-[28px]
                        border border-slate-200/80
                        bg-white/90
                        p-6
                        shadow-[0_20px_50px_rgba(15,23,42,.08)]
                        backdrop-blur-xl
                    ">
                    <div class="
                            flex h-16 w-16
                            shrink-0 items-center
                            justify-center
                            rounded-2xl
                            {{ $i === 5 ? 'bg-amber-50 text-amber-500' : 'bg-brand-primary/10 text-brand-primary' }}
                        ">
                        <i data-lucide="{{ $benefit['icon'] ?? 'circle' }}" class="h-8 w-8"></i>
                    </div>

                    <div>
                        <h3 class="
                                text-lg font-extrabold
                                text-brand-heading
                            ">
                            {{ $benefit['title'] ?? '' }}
                        </h3>

                        <span class="
                                mt-3 block h-0.5
                                w-6 rounded-full
                                bg-brand-primary
                            "></span>

                        <p class="
                                mt-3 text-sm
                                leading-6
                                text-slate-600
                            ">
                            {{ $benefit['description'] ?? '' }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- MOBILE / TABLET VERSION -->
        <div class="mt-12 lg:hidden">
            <!-- Mobile logo -->
            <div class="
                    mx-auto mb-8 flex
                    h-48 w-48
                    items-center justify-center
                    rounded-full
                    border border-blue-200
                    bg-white
                    text-center
                    shadow-[0_20px_50px_rgba(37,99,235,.15)]
                ">
                <div>
                    <div class="
                            mx-auto flex
                            h-14 w-14
                            items-center justify-center
                            rounded-2xl
                            bg-brand-primary
                            text-white
                        ">
                        <i data-lucide="{{ $c['why']['hub_icon'] ?? 'laptop-minimal-check' }}" class="h-7 w-7"></i>
                    </div>

                    <strong class="
                            mt-3 block
                            text-xl font-extrabold
                            text-brand-heading
                        ">
                        {{ $c['why']['hub_title'] ?? '' }}
                    </strong>

                    <span class="
                            mt-1 block text-xs
                            font-semibold text-slate-500
                        ">
                        {{ $c['why']['hub_subtitle'] ?? '' }}
                    </span>
                </div>
            </div>

            <!-- Mobile cards -->
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($c['why']['benefits'] ?? [] as $i => $benefit)
                    <article class="
                            why-card-mobile
                            rounded-3xl
                            border border-slate-200
                            bg-white p-5
                            shadow-sm
                        ">
                        <div class="
                                flex h-12 w-12
                                items-center justify-center
                                rounded-2xl {{ $i === 5 ? 'bg-amber-50 text-amber-500' : 'bg-brand-primary/10 text-brand-primary' }}
                            ">
                            <i data-lucide="{{ $benefit['icon'] ?? 'circle' }}" class="h-6 w-6"></i>
                        </div>

                        <h3 class="mt-4 font-extrabold text-brand-heading">
                            {{ $benefit['title'] ?? '' }}
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            {{ $benefit['description'] ?? '' }}
                        </p>
                    </article>
                @endforeach
            </div>
        </div>

        <!-- STATISTICS -->
        <div class="
                mt-14 grid
                overflow-hidden
                rounded-[28px]
                border border-slate-200/80
                bg-white/85
                shadow-[0_18px_50px_rgba(15,23,42,.06)]
                backdrop-blur-xl
                sm:grid-cols-2
                lg:grid-cols-4
            ">
            @php
                $statClasses = [
                    0 => 'border-b border-slate-200 sm:border-r lg:border-b-0',
                    1 => 'border-b border-slate-200 lg:border-b-0 lg:border-r',
                    2 => 'border-b border-slate-200 sm:border-b-0 sm:border-r',
                    3 => '',
                ];
            @endphp

            @foreach ($c['why']['stats'] ?? [] as $i => $stat)
                <div class="
                        flex items-center gap-4
                        p-6
                        {{ $statClasses[$i] ?? '' }}
                    ">
                    <span class="
                            flex h-12 w-12
                            shrink-0 items-center justify-center
                            rounded-2xl {{ $i === 3 ? 'bg-lime-50 text-lime-600' : 'bg-blue-50 text-blue-600' }}
                        ">
                        <i data-lucide="{{ $stat['icon'] ?? 'circle' }}" class="h-6 w-6"></i>
                    </span>

                    <div>
                        <strong class="
                                block text-2xl
                                font-extrabold
                                text-blue-700
                            ">
                            {{ $stat['value'] ?? '' }}
                        </strong>

                        <span class="text-sm text-slate-500">
                            {{ $stat['label'] ?? '' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

