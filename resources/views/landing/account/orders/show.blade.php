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
    </style>

    @php
        $steps = [
            ['key' => 'ordered', 'label' => 'Besteld', 'done' => true],
            ['key' => 'paid', 'label' => 'Betaald', 'done' => $order->payment_status === 'paid'],
            ['key' => 'shipped', 'label' => $order->shipping_method === 'pickup' ? 'Klaar voor afhalen' : 'Verzonden', 'done' => in_array($order->order_status, ['shipped', 'completed'], true)],
            ['key' => 'done', 'label' => 'Afgerond', 'done' => $order->order_status === 'completed'],
        ];
        $isCancelled = $order->order_status === 'cancelled';
    @endphp

    <main class="webshop-root bg-[#f8fafc] text-slate-900">

        {{-- HERO --}}
        <section class="bg-white border-b border-slate-100">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="py-7">
                    <div class="reveal text-[12px] text-slate-500 flex items-center gap-2">
                        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Home</a>
                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        <a href="{{ route('account.orders.index') }}" class="hover:text-blue-600 transition">Mijn bestellingen</a>
                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        <span class="text-slate-700">{{ $order->order_number }}</span>
                    </div>
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-[30px] sm:text-[38px] font-black tracking-[-.04em] leading-none text-[#0b1734]">
                                {{ $order->order_number }}
                            </h1>
                            <p class="mt-2 text-[13px] text-slate-500">
                                Geplaatst op {{ $order->created_at->format('d-m-Y H:i') }} · {{ $order->shipping_method === 'pickup' ? 'Afhalen in Apeldoorn' : 'Bezorging' }}
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full border px-3 py-1.5 text-[11px] font-bold {{ $orderStatusStyles[$order->order_status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                {{ $orderStatusLabels[$order->order_status] ?? $order->order_status }}
                            </span>
                            <span class="rounded-full border px-3 py-1.5 text-[11px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
                            </span>
                            @if($order->invoice)
                                <a href="{{ route('account.orders.invoice', $order->order_number) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-[12px] font-bold text-white hover:bg-blue-700 transition">
                                    <i data-lucide="file-down" class="w-4 h-4"></i> Factuur ({{ $order->invoice->invoice_number }})
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-8">
            <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
                <div class="grid gap-6 lg:grid-cols-[1fr_340px]">

                    <div class="space-y-6">
                        {{-- STATUS STEPS --}}
                        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                            <h2 class="text-[15px] font-extrabold text-[#0b1734]">Status</h2>
                            @if($isCancelled)
                                <div class="mt-4 flex items-center gap-3 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3">
                                    <i data-lucide="x-circle" class="w-5 h-5 text-rose-600"></i>
                                    <p class="text-[13px] font-semibold text-rose-700">Deze bestelling is geannuleerd. Neem contact met ons op voor vragen.</p>
                                </div>
                            @else
                                <ol class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($steps as $step)
                                        <li class="rounded-xl border px-3 py-3 text-center {{ $step['done'] ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }}">
                                            <div class="mx-auto flex w-8 h-8 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }}">
                                                @if($step['done'])
                                                    <i data-lucide="check" class="w-4 h-4"></i>
                                                @else
                                                    <span class="w-2 h-2 rounded-full bg-current"></span>
                                                @endif
                                            </div>
                                            <p class="mt-2 text-[11px] font-bold {{ $step['done'] ? 'text-emerald-700' : 'text-slate-500' }}">{{ $step['label'] }}</p>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>

                        {{-- ITEMS --}}
                        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5 sm:p-6">
                            <h2 class="text-[15px] font-extrabold text-[#0b1734]">Artikelen ({{ $order->items->count() }})</h2>
                            <div class="mt-4 divide-y divide-slate-100">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between gap-4 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400">
                                                <i data-lucide="package" class="w-5 h-5"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-bold text-slate-800">{{ $item->product_name }}</p>
                                                <p class="text-[11px] text-slate-500">Aantal: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <span class="shrink-0 text-[14px] font-extrabold text-[#0b1734]">€{{ number_format($item->total_price, 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <dl class="mt-4 space-y-2 border-t border-slate-100 pt-4 text-[13px]">
                                <div class="flex justify-between text-slate-600">
                                    <dt>Subtotaal (incl. btw)</dt>
                                    <dd class="font-semibold">€{{ number_format($order->subtotal, 2, ',', '.') }}</dd>
                                </div>
                                @if((float) $order->discount_amount > 0)
                                    <div class="flex justify-between text-emerald-600">
                                        <dt>Korting{{ $order->discount_code ? ' ('.$order->discount_code.')' : '' }}</dt>
                                        <dd class="font-semibold">− €{{ number_format($order->discount_amount, 2, ',', '.') }}</dd>
                                    </div>
                                @endif
                                <div class="flex justify-between text-slate-600">
                                    <dt>{{ $order->shipping_method === 'pickup' ? 'Afhalen' : 'Verzendkosten' }}</dt>
                                    <dd class="font-semibold">{{ (float) $order->shipping_cost > 0 ? '€'.number_format($order->shipping_cost, 2, ',', '.') : 'Gratis' }}</dd>
                                </div>
                                <div class="flex justify-between border-t border-slate-100 pt-3 text-[16px] font-black text-[#0b1734]">
                                    <dt>Totaal</dt>
                                    <dd>€{{ number_format($order->total_price, 2, ',', '.') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    {{-- SIDE --}}
                    <aside class="space-y-6">
                        <div class="reveal rounded-2xl border border-slate-200 bg-white p-5">
                            <h2 class="text-[14px] font-extrabold text-[#0b1734]">Gegevens</h2>
                            <dl class="mt-3 space-y-2.5 text-[12px]">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-slate-500">Bestelnummer</dt>
                                    <dd class="font-bold text-right">{{ $order->order_number }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-slate-500">Klantnummer</dt>
                                    <dd class="font-bold text-right">{{ $order->klantnummer ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-slate-500">E-mail</dt>
                                    <dd class="font-bold text-right break-all">{{ $order->customer_email }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-slate-500">Betaalmethode</dt>
                                    <dd class="font-bold text-right capitalize">{{ $order->payment_method ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>

                        @if($order->billingAddress)
                            <div class="reveal rounded-2xl border border-slate-200 bg-white p-5">
                                <h2 class="text-[14px] font-extrabold text-[#0b1734]">Adres</h2>
                                <p class="mt-3 text-[12px] leading-6 text-slate-600">
                                    {{ $order->billingAddress->street_address }}<br>
                                    {{ $order->billingAddress->postal_code }} {{ $order->billingAddress->city }}
                                </p>
                            </div>
                        @endif

                        <a href="{{ route('contact') }}" class="reveal flex items-center gap-3 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-[#edf5ff] p-5 transition hover:border-blue-200">
                            <div class="flex w-11 h-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <i data-lucide="headset" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <p class="text-[13px] font-extrabold text-[#0b1734]">Vraag over je bestelling?</p>
                                <p class="text-[11px] text-slate-500">Wij helpen je graag verder.</p>
                            </div>
                        </a>
                    </aside>

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
    </script>
@endsection