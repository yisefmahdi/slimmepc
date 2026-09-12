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
        .order-card {
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }
        .order-card:hover {
            transform: translateY(-3px);
            border-color: #bfdbfe;
            box-shadow: 0 18px 42px rgba(15,23,42,.08);
        }
    </style>

    <main class="webshop-root bg-[#f8fafc] text-slate-900">

        {{-- HERO --}}
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid lg:grid-cols-[1fr_auto] items-center gap-5 min-h-[220px] py-7">
                    <div class="reveal">
                        <div class="text-[12px] text-slate-500 flex items-center gap-2">
                            <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a>
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            <span class="text-slate-700">Mijn bestellingen</span>
                        </div>
                        <span class="mt-4 inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[.08em] text-blue-600">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Mijn account
                        </span>
                        <h1 class="mt-2 text-[38px] sm:text-[44px] font-black tracking-[-.04em] leading-none text-[#0b1734]">
                            Mijn bestellingen
                        </h1>
                        <p class="mt-4 max-w-[420px] text-[14px] sm:text-[15px] leading-6 text-slate-500">
                            Bekijk de status van je bestellingen en download je facturen.
                        </p>
                    </div>
                    <div class="reveal">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-trust-card">
                            <div class="flex gap-3">
                                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <i data-lucide="package" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-[13px] font-extrabold">
                                        {{ $orders->total() }} bestelling{{ $orders->total() !== 1 ? 'en' : '' }}
                                    </h3>
                                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                        {{ $orders->total() > 0 ? 'Al je bestellingen op één plek.' : 'Je hebt nog niets besteld.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ORDERS --}}
        <section class="py-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">

                @if($orders->isEmpty())
                    <div class="reveal rounded-2xl border-2 border-dashed border-[#DCE4EF] bg-white p-8 sm:p-12 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#F8FAFF] border border-[#DDE6F4] text-[#0759F5]">
                            <i data-lucide="package-open" class="w-8 h-8"></i>
                        </div>
                        <h3 class="mt-4 text-[18px] font-bold text-[#07173A]">Nog geen bestellingen</h3>
                        <p class="mt-2 text-[14px] text-[#3E547F]">Zodra je iets bestelt, verschijnt het hier met status en factuur.</p>
                        <a href="{{ route('home') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-[14px] font-bold text-white hover:bg-blue-700 transition">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i> Naar de webshop
                        </a>
                    </div>
                @else
                    <div class="grid gap-4">
                        @foreach($orders as $order)
                            <article class="order-card reveal rounded-2xl border border-slate-200 bg-white p-4 sm:p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start gap-4">
                                        <div class="hidden sm:flex w-12 h-12 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                            <i data-lucide="receipt-text" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-[15px] font-extrabold text-[#0b1734]">{{ $order->order_number }}</span>
                                                <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $orderStatusStyles[$order->order_status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                                    {{ $orderStatusLabels[$order->order_status] ?? $order->order_status }}
                                                </span>
                                                <span class="rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                                    {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                                                </span>
                                            </div>
                                            <p class="mt-1.5 text-[12px] text-slate-500">
                                                {{ $order->created_at->format('d-m-Y H:i') }} · {{ $order->items_count }} artikel{{ $order->items_count !== 1 ? 'en' : '' }} · {{ $order->shipping_method === 'pickup' ? 'Afhalen' : 'Bezorging' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-3">
                                        <span class="text-[18px] font-black text-[#0b1734]">€{{ number_format($order->total_price, 2, ',', '.') }}</span>
                                        <a href="{{ route('account.orders.show', $order->order_number) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-[12px] font-bold text-white hover:bg-blue-700 transition">
                                            Bekijken
                                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if($orders->hasPages())
                        <div class="reveal mt-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="text-[11px] text-slate-500">
                                Toont {{ $orders->firstItem() }}–{{ $orders->lastItem() }} van de {{ $orders->total() }} resultaten
                            </div>
                            <div class="flex items-center gap-1">
                                {{ $orders->links('vendor.pagination.webshop') }}
                            </div>
                        </div>
                    @endif
                @endif

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
    @include('landing.partials.ai-chat')

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
    </script>
@endsection