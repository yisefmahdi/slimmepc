@extends('landing.layouts.app')

@section('content')
    @include('landing.partials.header')

    @php
        $placeholderSrc = asset('assets/img/product-placeholder.jpg');
        $resolveImg = function($p) use ($placeholderSrc) {
            if (!$p) return $placeholderSrc;
            if (str_starts_with($p, 'http')) return $p;
            if (str_starts_with($p, 'assets/')) return asset($p);
            if (str_starts_with($p, 'storage/')) return asset($p);
            return asset('storage/' . ltrim($p, '/'));
        };
        $user = auth()->user();
        $prefill = [
            'email' => old('email', $user->email ?? ''),
            'first_name' => old('first_name', explode(' ', $user->name ?? '')[0] ?? ''),
            'last_name' => old('last_name', implode(' ', array_slice(explode(' ', $user->name ?? ''), 1)) ?? ''),
            'phone' => old('phone', $user->phone ?? ''),
        ];
    @endphp

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: '#155EEF', brandDark: '#0A45C2', navy: '#07172F', softBlue: '#F4F8FF', borderBlue: '#DCE6F5' },
                    boxShadow: { card: '0 8px 30px rgba(11,37,80,.06)', floating: '0 20px 55px rgba(15,54,115,.10)', blue: '0 12px 30px rgba(21,94,239,.20)' }
                }
            }
        }
    </script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
        .field { transition: all .2s ease; }
        .field:focus { outline: none; border-color: #155EEF; box-shadow: 0 0 0 4px rgba(21,94,239,.08); background: white; }
        .field-error { border-color: #ef4444 !important; }
        .shipping-card { transition: all .22s ease; }
        .shipping-card:hover { transform: translateY(-1px); border-color: #98B7F8; }
        .shipping-card.active { border-color: #155EEF; background: linear-gradient(135deg, rgba(21,94,239,.055), rgba(255,255,255,1)); box-shadow: 0 0 0 1px #155EEF, 0 8px 22px rgba(21,94,239,.08); }
        .section-hidden { opacity: 0; max-height: 0; overflow: hidden; transform: translateY(-10px); margin: 0 !important; padding-top: 0 !important; padding-bottom: 0 !important; border-width: 0 !important; transition: all .35s ease; }
        .section-visible { opacity: 1; max-height: 1200px; transform: translateY(0); transition: all .4s ease; }
    </style>

    <main class="min-h-screen bg-[#FAFCFF] text-navy">
        <div class="relative z-10 max-w-[1450px] mx-auto px-4 sm:px-6 lg:px-10 xl:px-12 py-8 lg:py-11">

            <div>
                <div class="inline-flex items-center gap-2 text-xs font-semibold text-brand bg-blue-50 border border-blue-100 px-3 py-1.5 rounded-full mb-3">
                    <i data-lucide="lock" class="w-3 h-3"></i> Veilig afrekenen
                </div>
                <h1 class="text-3xl md:text-[36px] leading-tight font-bold tracking-tight">Afrekenen</h1>
                <div class="flex items-center gap-2 mt-2 text-sm text-slate-500">
                    <a href="{{ route('home') }}" class="hover:text-brand transition">Home</a>
                    <span class="text-[9px]">›</span>
                    <a href="{{ route('cart.index') }}" class="hover:text-brand transition">Winkelwagen</a>
                    <span class="text-[9px]">›</span>
                    <span class="text-slate-700">Afrekenen</span>
                </div>
            </div>

            <div id="checkoutErrors" class="hidden mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"></div>

            <form id="checkoutForm" novalidate class="mt-8 grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_470px] gap-6 items-start">
                @csrf
                <div class="space-y-5">
                    {{-- CONTACT --}}
                    <section class="bg-white border border-borderBlue rounded-2xl shadow-card p-5 md:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand flex items-center justify-center shrink-0"><i data-lucide="user" class="w-5 h-5"></i></div>
                            <div class="flex-1">
                                <h2 class="font-bold text-lg">Contactgegevens</h2>
                                <p class="text-sm text-slate-500 mt-1">We gebruiken deze gegevens voor je bestelling.</p>
                            </div>
                        </div>
                        <div class="mt-6 md:pl-[60px]">
                            <label class="block text-sm font-semibold mb-2">E-mailadres <span class="text-brand">*</span></label>
                            <input id="email" name="email" type="email" required value="{{ $prefill['email'] }}" placeholder="voorbeeld@mail.nl" class="field w-full h-12 bg-slate-50/60 border border-slate-300 rounded-xl pl-4 pr-4 text-sm">
                            <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="email"></p>
                            <label class="flex items-center gap-3 mt-4 cursor-pointer select-none">
                                <input type="checkbox" name="newsletter" value="1" checked class="w-4 h-4 accent-[#155EEF]">
                                <span class="text-sm text-slate-600">Ik wil graag nieuws en aanbiedingen ontvangen.</span>
                            </label>
                        </div>
                    </section>

                    {{-- SAVED ADDRESSES --}}
                    @if($savedAddresses->isNotEmpty())
                        <section class="bg-white border border-borderBlue rounded-2xl shadow-card p-5 md:p-7">
                            <h2 class="font-bold text-lg">Opgeslagen adressen</h2>
                            <p class="text-sm text-slate-500 mt-1">Kies een eerder gebruikt adres of vul een nieuw adres in.</p>
                            <select id="savedAddress" name="saved_address_id" class="field mt-4 w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <option value="">Nieuw adres invoeren</option>
                                @foreach($savedAddresses as $a)
                                    <option value="{{ $a->id }}">{{ $a->fullName() }} — {{ $a->fullStreet() }}, {{ $a->postcode }} {{ $a->city }}</option>
                                @endforeach
                            </select>
                        </section>
                    @endif

                    {{-- ADDRESS --}}
                    <section id="addressSection" class="section-visible bg-white border border-borderBlue rounded-2xl shadow-card p-5 md:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand flex items-center justify-center shrink-0"><i data-lucide="map-pin" class="w-5 h-5"></i></div>
                            <div>
                                <h2 class="font-bold text-lg">Verzendadres</h2>
                                <p class="text-sm text-slate-500 mt-1">Waar mogen we jouw bestelling bezorgen?</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4 mt-6 md:pl-[60px]">
                            <div>
                                <label class="block text-sm font-semibold mb-2">Voornaam *</label>
                                <input name="first_name" type="text" required value="{{ $prefill['first_name'] }}" placeholder="Voornaam" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="first_name"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Achternaam *</label>
                                <input name="last_name" type="text" required value="{{ $prefill['last_name'] }}" placeholder="Achternaam" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="last_name"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Straat en huisnummer *</label>
                                <div class="flex gap-2">
                                    <input name="street" type="text" required placeholder="Straat" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                    <input name="house_number" type="text" required placeholder="Nr." class="field w-24 h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                </div>
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="street"></p>
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="house_number"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Toevoeging <span class="text-slate-400 font-normal">(optioneel)</span></label>
                                <input name="addition" type="text" placeholder="Bijv. A, bis, 2e" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Postcode *</label>
                                <input name="postcode" type="text" required placeholder="1234 AB" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="postcode"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Plaats *</label>
                                <input name="city" type="text" required placeholder="Apeldoorn" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="city"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Land *</label>
                                <select name="country" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                    <option>Nederland</option><option>België</option><option>Duitsland</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2">Telefoonnummer *</label>
                                <input name="phone" type="tel" required value="{{ $prefill['phone'] }}" placeholder="06 12345678" class="field w-full h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm">
                                <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="phone"></p>
                            </div>
                        </div>
                    </section>

                    {{-- SHIPPING --}}
                    <section class="bg-white border border-borderBlue rounded-2xl shadow-card p-5 md:p-7">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-brand flex items-center justify-center shrink-0"><i data-lucide="truck" class="w-5 h-5"></i></div>
                            <div>
                                <h2 class="font-bold text-lg">Verzendmethode</h2>
                                <p class="text-sm text-slate-500 mt-1">Kies hoe je jouw bestelling wilt ontvangen.</p>
                            </div>
                        </div>
                        <div class="mt-6 md:pl-[60px] space-y-3">
                            @foreach($rates as $rate)
                                <label class="shipping-card {{ $rate->slug === $method ? 'active' : '' }} relative flex gap-4 items-center border border-slate-300 rounded-xl px-4 py-4 cursor-pointer">
                                    <input type="radio" name="shipping_method" value="{{ $rate->slug }}" {{ $rate->slug === $method ? 'checked' : '' }} class="shipping-radio w-5 h-5 accent-[#155EEF] shrink-0">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-brand shrink-0">
                                        <i data-lucide="{{ $rate->slug === 'pickup' ? 'store' : 'truck' }}" class="w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-semibold text-sm">{{ $rate->name }}</span>
                                        <span class="block text-xs text-slate-500 mt-1">
                                            @if($rate->slug === 'pickup') We laten je weten zodra de bestelling klaarstaat in Apeldoorn
                                            @elseif($rate->free_above) Gratis vanaf €{{ number_format($rate->free_above, 2, ',', '.') }} · anders €{{ number_format($rate->price, 2, ',', '.') }}
                                            @else €{{ number_format($rate->price, 2, ',', '.') }} @endif
                                        </span>
                                    </div>
                                    <span class="font-bold text-sm text-emerald-600" data-rate-price="{{ $rate->slug }}">
                                        @if($rate->slug === 'pickup' || $rate->price == 0) Gratis @else €{{ number_format($rate->price, 2, ',', '.') }} @endif
                                    </span>
                                </label>
                            @endforeach
                            <div id="pickupInfo" class="{{ $method === 'pickup' ? '' : 'hidden' }} rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-sm">
                                <div class="font-semibold">Afhalen bij Slimme-PC</div>
                                <p class="text-slate-600 text-xs mt-1">Je hoeft geen verzendadres in te vullen. Je ontvangt een bericht zodra je bestelling klaarstaat.</p>
                            </div>
                            <p class="err hidden mt-1 text-xs font-semibold text-red-600" data-err="shipping_method"></p>
                            <button id="payBtn" type="submit" class="w-full h-[52px] mt-5 rounded-xl bg-gradient-to-r from-brand to-[#0C4CD9] text-white font-semibold shadow-blue hover:-translate-y-[1px] transition-all">
                                <span id="payBtnLabel" class="flex items-center justify-center gap-3">Verder naar betaling <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                            </button>
                            <div class="flex justify-center items-center gap-2 text-xs text-slate-500 mt-3">
                                <i data-lucide="lock" class="w-3 h-3"></i> Je gegevens worden veilig versleuteld verzonden.
                            </div>
                        </div>
                    </section>
                </div>

                {{-- SUMMARY --}}
                <aside class="xl:sticky xl:top-6 space-y-5">
                    <section class="bg-white border border-borderBlue rounded-2xl shadow-floating overflow-hidden">
                        <div class="px-6 py-5 border-b border-slate-100">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="font-bold text-lg">Jouw bestelling</h2>
                                    <span class="text-xs text-slate-500" id="summaryCount">{{ $totals['count'] }} producten</span>
                                </div>
                                <a href="{{ route('cart.index') }}" class="text-sm text-brand font-semibold hover:underline">Wijzig</a>
                            </div>
                        </div>
                        <div class="px-6">
                            @foreach($cart->items as $item)
                                @php $pImg = $item->product ? ($item->product->main_image ?: ($item->product->gallery_images[0] ?? null)) : null; @endphp
                                <div class="flex gap-4 py-5 border-b border-slate-100">
                                    <div class="w-20 h-20 rounded-xl bg-[#F7F9FC] border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden">
                                        <img src="{{ $resolveImg($pImg) }}" class="w-16 h-16 object-contain rounded-lg" alt="">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-sm leading-5">{{ $item->product->title ?? 'Product' }}</h3>
                                        <div class="mt-3 flex justify-between items-center">
                                            <span class="text-xs bg-slate-100 rounded-md px-2 py-1">{{ $item->quantity }} ×</span>
                                            <span class="font-bold">€{{ number_format($item->price_snapshot * $item->quantity, 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="px-6 py-5">
                            {{-- coupon --}}
                            <div id="couponBlock" class="mb-4">
                                @if($totals['coupon'])
                                    <div class="flex items-center justify-between text-sm bg-emerald-50 border border-emerald-200 rounded-xl px-3 py-2">
                                        <span class="font-semibold text-emerald-700">{{ $totals['coupon']->code }} (−€{{ number_format($totals['discount'], 2, ',', '.') }})</span>
                                        <button type="button" id="removeCouponBtn" class="text-emerald-700 hover:underline text-xs font-bold">Verwijder</button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <input id="couponCode" type="text" placeholder="Kortingscode" class="field flex-1 h-11 border border-slate-300 bg-slate-50/60 rounded-xl px-4 text-sm uppercase">
                                        <button type="button" id="applyCouponBtn" class="h-11 px-4 rounded-xl border border-slate-300 text-sm font-semibold hover:bg-slate-50">Toepassen</button>
                                    </div>
                                    <p id="couponMsg" class="hidden mt-1 text-xs font-semibold"></p>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Subtotaal</span><span id="sumSubtotal">€{{ number_format($totals['subtotal'], 2, ',', '.') }}</span></div>
                                <div id="discountRow" class="{{ $totals['discount'] > 0 ? '' : 'hidden' }} flex justify-between text-sm"><span class="text-slate-500">Korting</span><span id="sumDiscount" class="text-emerald-600 font-semibold">−€{{ number_format($totals['discount'], 2, ',', '.') }}</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">Verzending</span><span id="sumShipping" class="font-semibold {{ $totals['shipping'] == 0 ? 'text-emerald-600' : '' }}">@if($totals['shipping'] == 0) Gratis @else €{{ number_format($totals['shipping'], 2, ',', '.') }} @endif</span></div>
                                <div class="flex justify-between text-sm"><span class="text-slate-500">BTW (21% incl.)</span><span id="sumTax">€{{ number_format($totals['tax'], 2, ',', '.') }}</span></div>
                            </div>
                            <div class="border-t border-slate-200 mt-5 pt-5">
                                <div class="flex justify-between items-end">
                                    <div><span class="block text-lg font-bold">Totaal</span><span class="block text-xs text-slate-500 mt-1">Inclusief btw</span></div>
                                    <span id="sumTotal" class="block text-2xl font-bold text-brand">€{{ number_format($totals['total'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-[#F4F8FF] to-[#F9FBFF] border-t border-blue-100 px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white shadow-sm rounded-full flex items-center justify-center text-brand"><i data-lucide="shield-check" class="w-4 h-4"></i></div>
                                <div><div class="font-semibold text-xs">Veilig betalen</div><div class="text-[11px] text-slate-500 mt-0.5">Beveiligde SSL-verbinding via Mollie</div></div>
                            </div>
                        </div>
                    </section>
                    <section class="bg-white border border-borderBlue rounded-2xl p-4">
                        <div class="text-center text-[11px] uppercase tracking-[.14em] font-semibold text-slate-400 mb-3">Veilig betalen met</div>
                        <div class="grid grid-cols-5 gap-2 text-center">
                            <div class="h-11 rounded-lg border flex items-center justify-center text-[11px] font-bold text-pink-700">iDEAL</div>
                            <div class="h-11 rounded-lg border flex items-center justify-center text-[9px] font-bold text-blue-700">Bancontact</div>
                            <div class="h-11 rounded-lg border flex items-center justify-center font-bold text-blue-600 text-xs">PayPal</div>
                            <div class="h-11 rounded-lg border flex items-center justify-center font-bold text-blue-700 text-xs">VISA</div>
                            <div class="h-11 rounded-lg border flex items-center justify-center"><div class="flex"><span class="block w-5 h-5 bg-red-500 rounded-full"></span><span class="block w-5 h-5 bg-yellow-400 rounded-full -ml-2"></span></div></div>
                        </div>
                    </section>
                </aside>
            </form>
        </div>
    </main>

    @include('landing.partials.footer')
    @include('landing.partials.floating')

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name=_token]')?.value || '';
        const money = v => new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v);
        const form = document.getElementById('checkoutForm');
        const payBtn = document.getElementById('payBtn');
        const payBtnLabel = document.getElementById('payBtnLabel');
        const errBox = document.getElementById('checkoutErrors');

        // shipping toggle
        const radios = document.querySelectorAll('.shipping-radio');
        const cards = document.querySelectorAll('.shipping-card');
        const addressSection = document.getElementById('addressSection');
        const pickupInfo = document.getElementById('pickupInfo');
        radios.forEach(r => r.addEventListener('change', async function () {
            cards.forEach(c => c.classList.remove('active'));
            this.closest('.shipping-card').classList.add('active');
            const isPickup = this.value === 'pickup';
            addressSection.classList.toggle('section-hidden', isPickup);
            addressSection.classList.toggle('section-visible', !isPickup);
            pickupInfo.classList.toggle('hidden', !isPickup);
            try {
                const res = await fetch('{{ route('checkout.totals') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ shipping_method: this.value })
                });
                const t = await res.json();
                updateTotals(t);
            } catch (e) {}
        }));

        function updateTotals(t) {
            document.getElementById('sumSubtotal').textContent = money(t.subtotal);
            document.getElementById('sumShipping').textContent = t.shipping == 0 ? 'Gratis' : money(t.shipping);
            document.getElementById('sumShipping').className = 'font-semibold ' + (t.shipping == 0 ? 'text-emerald-600' : '');
            document.getElementById('sumTax').textContent = money(t.tax);
            document.getElementById('sumTotal').textContent = money(t.total);
            const dRow = document.getElementById('discountRow');
            if (dRow) {
                dRow.classList.toggle('hidden', !(t.discount > 0));
                document.getElementById('sumDiscount').textContent = '−' + money(t.discount);
            }
        }

        // saved address fill
        const savedSel = document.getElementById('savedAddress');
        @php
            $savedJson = [];
            foreach ($savedAddresses as $sa) {
                $savedJson[$sa->id] = [
                    'first_name' => $sa->first_name, 'last_name' => $sa->last_name,
                    'street' => $sa->street, 'house_number' => $sa->house_number,
                    'addition' => $sa->addition, 'postcode' => $sa->postcode,
                    'city' => $sa->city, 'phone' => $sa->phone,
                ];
            }
        @endphp
        const savedData = @json($savedJson);
        if (savedSel) savedSel.addEventListener('change', function () {
            const d = savedData[this.value];
            if (!d) return;
            form.querySelector('[name=first_name]').value = d.first_name || '';
            form.querySelector('[name=last_name]').value = d.last_name || '';
            form.querySelector('[name=street]').value = d.street || '';
            form.querySelector('[name=house_number]').value = d.house_number || '';
            form.querySelector('[name=addition]').value = d.addition || '';
            form.querySelector('[name=postcode]').value = d.postcode || '';
            form.querySelector('[name=city]').value = d.city || '';
            form.querySelector('[name=phone]').value = d.phone || '';
        });

        // coupon
        document.getElementById('applyCouponBtn')?.addEventListener('click', async () => {
            const code = document.getElementById('couponCode').value.trim();
            const msg = document.getElementById('couponMsg');
            if (!code) return;
            try {
                const res = await fetch('{{ route('cart.coupon.apply') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ code })
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Fout');
                msg.textContent = data.message; msg.className = 'mt-1 text-xs font-semibold text-emerald-600'; msg.classList.remove('hidden');
                updateTotals(data.totals);
                setTimeout(() => location.reload(), 800);
            } catch (e) {
                msg.textContent = e.message; msg.className = 'mt-1 text-xs font-semibold text-red-600'; msg.classList.remove('hidden');
            }
        });
        document.getElementById('removeCouponBtn')?.addEventListener('click', async () => {
            await fetch('{{ route('cart.coupon.remove') }}', { method: 'DELETE', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
            location.reload();
        });

        // submit → create order → redirect to Mollie (afspraak-style inline errors)
        function clearCheckoutErrors() {
            errBox.classList.add('hidden');
            form.querySelectorAll('p.field-error-dynamic').forEach(el => el.remove());
            form.querySelectorAll('.err').forEach(el => el.classList.add('hidden'));
            form.querySelectorAll('.field').forEach(el => el.classList.remove('field-error', '!border-red-500'));
        }

        function showCheckoutErrors(errors) {
            clearCheckoutErrors();
            let firstInput = null;
            Object.entries(errors).forEach(([f, msgs]) => {
                const msg = Array.isArray(msgs) ? msgs[0] : msgs;
                const inp = form.querySelector(`[name="${f}"]`);
                const staticP = form.querySelector(`[data-err="${f}"]`);
                if (staticP) { staticP.textContent = msg; staticP.classList.remove('hidden'); }
                if (inp && inp.type !== 'radio') {
                    inp.classList.add('field-error', '!border-red-500');
                    if (!staticP) {
                        const err = document.createElement('p');
                        err.className = 'field-error-dynamic mt-1 text-xs font-semibold text-red-600';
                        err.textContent = msg;
                        inp.insertAdjacentElement('afterend', err);
                    }
                    if (!firstInput) firstInput = inp;
                }
            });
            const firstMsg = Object.values(errors)[0];
            errBox.textContent = 'Controleer de gemarkeerde velden: ' + (Array.isArray(firstMsg) ? firstMsg[0] : firstMsg);
            errBox.classList.remove('hidden');
            if (firstInput) {
                firstInput.focus({ preventScroll: true });
                firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // clear a field's error while typing (like dashboard forms)
        form.addEventListener('input', (e) => {
            const inp = e.target.closest('.field');
            if (!inp || !inp.name) return;
            inp.classList.remove('field-error', '!border-red-500');
            const staticP = form.querySelector(`[data-err="${inp.name}"]`);
            if (staticP) staticP.classList.add('hidden');
            const dyn = inp.parentElement?.querySelector('p.field-error-dynamic');
            if (dyn) dyn.remove();
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearCheckoutErrors();
            payBtn.disabled = true;
            payBtnLabel.innerHTML = 'Bezig met verwerken…';
            const fd = new FormData(form);
            const payload = Object.fromEntries(fd.entries());
            try {
                const res = await fetch('{{ route('checkout.store') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json().catch(() => ({}));
                if (res.ok && data.redirect) { window.location.href = data.redirect; return; }
                if (res.status === 422 && data.errors) {
                    showCheckoutErrors(data.errors);
                    return;
                }
                if (res.status === 419) {
                    throw new Error('Sessie verlopen. Vernieuw de pagina en probeer het opnieuw.');
                }
                throw new Error(data.message || 'Er ging iets mis.');
            } catch (err) {
                // don't double-show when inline errors are already visible
                if (!form.querySelector('.field-error') && !form.querySelector('.err:not(.hidden)') && !form.querySelector('p.field-error-dynamic')) {
                    errBox.textContent = err.message;
                    errBox.classList.remove('hidden');
                    errBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } finally {
                payBtn.disabled = false;
                payBtnLabel.innerHTML = 'Verder naar betaling';
                if (window.lucide) lucide.createIcons();
            }
        });
    </script>
@endsection
