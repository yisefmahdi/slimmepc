@extends('landing.layouts.app')

@section('content')
    <main>
        <section class="relative min-h-screen overflow-hidden bg-[#f7faff] px-4 py-8">
            <div class="pointer-events-none absolute -left-48 top-20 h-[520px] w-[520px] rounded-full bg-blue-200/35 blur-[120px]"></div>
            <div class="pointer-events-none absolute -right-48 top-32 h-[500px] w-[500px] rounded-full bg-sky-100/70 blur-[120px]"></div>
            <div class="pointer-events-none absolute bottom-[-180px] left-[12%] h-[420px] w-[420px] rounded-full bg-blue-100/60 blur-[100px]"></div>

            <div class="relative z-10 mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-[1020px] flex-col items-center justify-center gap-6 lg:flex-row lg:items-start">
                <!-- Form card -->
                <div class="w-full max-w-[760px] flex-1 rounded-[28px] border border-white/80 bg-white/95 px-6 py-7 shadow-[0_25px_80px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:px-8 sm:py-8">

                    <div class="mb-7 text-center">
                        <h1 class="text-3xl font-bold tracking-tight text-[#071b46] sm:text-[34px]">Betaling formulier</h1>
                        @if($pricing['is_member'])
                            <p class="mx-auto mt-3 inline-flex items-center gap-1.5 rounded-full bg-green-50 px-4 py-1.5 text-xs font-bold text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                Lid — korting wordt automatisch toegepast
                            </p>
                        @endif
                    </div>

                    <!-- Klantgegevens -->
                    <div class="mb-6 rounded-2xl border border-slate-100 bg-slate-50/80 px-5 py-4">
                        <dl class="space-y-2 text-sm text-[#071b46]">
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Klantnummer:</dt>
                                <dd>{{ $client->klantnummer ?: '—' }}</dd>
                            </div>
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Naam:</dt>
                                <dd>{{ $client->name ?: '—' }}</dd>
                            </div>
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Adres:</dt>
                                <dd>
                                    @php
                                        $streetLine = trim((string) ($client->street . ' ' . $client->house_number));
                                        $cityLine = trim((string) (($client->postcode ?? '') . ' ' . ($client->city ?? '')));
                                        $addressLine = trim($streetLine . ($streetLine && $cityLine ? ', ' : '') . $cityLine);
                                    @endphp
                                    {{ $addressLine ?: '—' }}
                                </dd>
                            </div>
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Email:</dt>
                                <dd class="break-all">{{ $client->email ?: '—' }}</dd>
                            </div>
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Telefoon:</dt>
                                <dd>{{ $client->phone ?: '—' }}</dd>
                            </div>
                            <div class="flex flex-wrap gap-x-1.5">
                                <dt class="font-bold">Datum:</dt>
                                <dd>{{ now()->format('d-m-Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div id="techFormError" class="mb-5 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"></div>

                    <form id="techForm" action="{{ route('technician.payment.store') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="klantnummer" value="{{ $client->klantnummer }}">

                        <div class="grid grid-cols-1 gap-x-7 gap-y-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="description" class="mb-1.5 block text-sm font-medium text-[#071b46]">Probleem omschrijven</label>
                                <textarea id="description" name="description" rows="2" placeholder="Korte omschrijving van het bezoek..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="work_done" class="mb-1.5 block text-sm font-medium text-[#071b46]">Werkzaamheden</label>
                                <textarea id="work_done" name="work_done" rows="2" placeholder="Wat is er gedaan?"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('work_done') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="advice" class="mb-1.5 block text-sm font-medium text-[#071b46]">Advies</label>
                                <textarea id="advice" name="advice" rows="2" placeholder="Advies voor de klant..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('advice') }}</textarea>
                            </div>
                            <div>
                                <label for="start_time" class="mb-1.5 block text-sm font-medium text-[#071b46]">Van *</label>
                                <input id="start_time" name="start_time" type="time" required value="{{ old('start_time') }}" oninput="window.techQuoteFallback()" onchange="window.techQuoteFallback()"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-[#071b46] outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                @error('start_time')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_time" class="mb-1.5 block text-sm font-medium text-[#071b46]">Tot *</label>
                                <input id="end_time" name="end_time" type="time" required value="{{ old('end_time') }}" oninput="window.techQuoteFallback()" onchange="window.techQuoteFallback()"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-[#071b46] outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                @error('end_time')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="coupon_code" class="mb-1.5 block text-sm font-medium text-[#071b46]">Heb je een kortingscode?</label>
                                <div class="flex gap-2">
                                    <input id="coupon_code" name="coupon_code" type="text" placeholder="Code" value="{{ old('coupon_code') }}"
                                        class="h-[46px] min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 text-sm uppercase text-[#071b46] outline-none transition placeholder:normal-case placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <button type="button" id="couponApplyBtn"
                                        class="inline-flex h-[46px] shrink-0 items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-4 text-sm font-bold text-blue-700 transition hover:bg-blue-100 disabled:opacity-60">
                                        Toepassen
                                    </button>
                                </div>
                                <p id="couponMsg" class="mt-1 hidden text-xs font-semibold"></p>
                                @error('coupon_code')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <button id="techSubmitBtn" type="submit"
                            class="mt-6 flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-semibold text-white shadow-[0_12px_28px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_16px_32px_rgba(0,91,234,0.32)] disabled:opacity-60">
                            <span id="techSubmitLabel">Verder naar betaling</span>
                        </button>
                    </form>
                </div>

                <!-- Prijsopbouw -->
                <aside id="priceBox" class="w-full rounded-[28px] border border-white/80 bg-white/95 px-6 py-7 shadow-[0_25px_80px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:px-7 lg:w-[300px] lg:shrink-0"
                    data-quarter="{{ (float) ($pricing['quarter_price'] ?? 0) }}"
                    data-travel="{{ (float) ($pricing['travel_cost'] ?? 0) }}"
                    data-freetravel="{{ !empty($pricing['member_free_travel']) ? '1' : '0' }}"
                    data-dtype="{{ $pricing['member_discount_type'] ?? 'none' }}"
                    data-dval="{{ (float) ($pricing['member_discount_value'] ?? 0) }}"
                    data-ismember="{{ !empty($pricing['is_member']) ? '1' : '0' }}">
                    <h2 class="text-lg font-bold text-[#071b46]">Aantaaluuren</h2>
                    <p class="mt-1 text-xs text-slate-500">Tarief €{{ number_format($pricing['hour_price'], 2, ',', '.') }}/uur · per kwartier afgerekend</p>
                    <div id="priceRows" class="mt-4 space-y-2 text-sm text-[#071b46]">
                        <div class="flex flex-wrap gap-x-1.5"><span class="font-semibold">Aantaaluuren:</span><span id="prAantal">—</span></div>
                        <div class="flex flex-wrap gap-x-1.5"><span class="font-semibold">Reiskost:</span><span id="prTravel">—</span></div>
                        <div id="prMemberRow" class="hidden flex-wrap gap-x-1.5"><span class="font-semibold text-green-700">Lidkorting:</span><span id="prMember" class="font-bold text-green-700">—</span></div>
                        <div id="prCouponRow" class="hidden flex-wrap gap-x-1.5"><span class="font-semibold text-green-700">Kortingscode:</span><span id="prCoupon" class="font-bold text-green-700">—</span></div>
                        <div class="flex flex-wrap gap-x-1.5 pt-3"><span class="font-semibold">Subtotaal (excl. btw):</span><span id="prSub">—</span></div>
                        <div class="flex flex-wrap gap-x-1.5 pt-3"><span class="font-semibold">Btw (21%):</span><span id="prBtw">—</span></div>
                        <div class="flex flex-wrap gap-x-1.5 pt-3 text-base font-bold"><span>Totaal (incl. btw):</span><span id="prTotal" class="font-black text-blue-700">—</span></div>
                        <p id="prHint" class="pt-1 text-xs text-slate-400">Vul start- en eindtijd in voor een berekening.</p>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <script>
    (function () {
        const form = document.getElementById('techForm');
        if (!form) return;
        const btn = document.getElementById('techSubmitBtn');
        const btnLabel = document.getElementById('techSubmitLabel');
        const errBox = document.getElementById('techFormError');
        const csrf = form.querySelector('input[name="_token"]')?.value || '';
        const klantnummer = form.querySelector('input[name="klantnummer"]')?.value || '';
        const startEl = document.getElementById('start_time');
        const endEl = document.getElementById('end_time');
        const couponEl = document.getElementById('coupon_code');
        const couponBtn = document.getElementById('couponApplyBtn');
        const couponMsg = document.getElementById('couponMsg');
        let quoteTimer = null;
        let appliedCoupon = '';

        const eur = v => '€ ' + Number(v || 0).toFixed(2).replace('.', ',');
        const PRICING = {
            quarter_price: {{ (float) ($pricing['quarter_price'] ?? 0) }},
            travel_cost: {{ (float) ($pricing['travel_cost'] ?? 0) }},
            member_free_travel: {{ !empty($pricing['member_free_travel']) ? 'true' : 'false' }},
            member_discount_type: '{{ $pricing['member_discount_type'] ?? 'none' }}',
            member_discount_value: {{ (float) ($pricing['member_discount_value'] ?? 0) }},
            is_member: {{ !empty($pricing['is_member']) ? 'true' : 'false' }},
        };

        function paintQuote(d) {
            document.getElementById('prAantal').textContent = Math.floor(d.minutes / 60) + 'uur ' + (d.minutes % 60) + 'min';
            document.getElementById('prTravel').textContent = eur(d.travel);
            const mRow = document.getElementById('prMemberRow');
            if (Number(d.member_discount) > 0) {
                mRow.classList.remove('hidden'); mRow.classList.add('flex');
                document.getElementById('prMember').textContent = '−' + eur(d.member_discount);
            } else { mRow.classList.add('hidden'); mRow.classList.remove('flex'); }
            document.getElementById('prSub').textContent = eur(d.subtotal);
            document.getElementById('prTotal').textContent = eur(d.total);
            document.getElementById('prBtw').textContent = eur(d.btw);
            document.getElementById('prHint').textContent = '';
        }

        function paintCoupon(d) {
            const cRow = document.getElementById('prCouponRow');
            if (Number(d.discount) > 0) {
                cRow.classList.remove('hidden'); cRow.classList.add('flex');
                document.getElementById('prCoupon').textContent = '−' + eur(d.discount);
            } else { cRow.classList.add('hidden'); cRow.classList.remove('flex'); }
            document.getElementById('prTotal').textContent = eur(d.total);
        }

        // Instant berekening in de browser (zelfde formule als de server; de server herberekent bij opslaan).
        function refreshQuote() {
            const hint = document.getElementById('prHint');
            if (!startEl.value || !endEl.value) return;
            const sp = startEl.value.split(':').map(Number);
            const ep = endEl.value.split(':').map(Number);
            if (sp.length < 2 || ep.length < 2) return;
            const minutes = (ep[0] * 60 + ep[1]) - (sp[0] * 60 + sp[1]);
            if (!(minutes >= 5)) {
                if (hint) { hint.textContent = 'De eindtijd moet na de starttijd zijn (minimaal 5 minuten).'; }
                return;
            }
            const quarters = Math.ceil(minutes / 15);
            const travel = PRICING.member_free_travel ? 0 : Number(PRICING.travel_cost || 0);
            const bruto = Math.round((quarters * Number(PRICING.quarter_price || 0) + travel) * 100) / 100;
            let memberDiscount = 0;
            if (PRICING.member_discount_type === 'percent' && Number(PRICING.member_discount_value) > 0) {
                memberDiscount = Math.round(bruto * Number(PRICING.member_discount_value) / 100 * 100) / 100;
            } else if (PRICING.member_discount_type === 'fixed' && Number(PRICING.member_discount_value) > 0) {
                memberDiscount = Math.min(Number(PRICING.member_discount_value), bruto);
            }
            const net = Math.round((bruto - memberDiscount) * 100) / 100;
            const btw = Math.round(net * 21 / 121 * 100) / 100;
            const subtotal = Math.round((net - btw) * 100) / 100;
            paintQuote({
                quarters: quarters,
                quarter_price: Number(PRICING.quarter_price || 0),
                travel: travel,
                member_discount: memberDiscount,
                total: net,
                btw: btw,
                subtotal: subtotal,
                minutes: minutes,
                is_member: PRICING.is_member,
            });
            appliedCoupon = '';
            paintCoupon({ discount: 0, total: net });
        }

        if (!startEl || !endEl) return;

        [startEl, endEl].forEach(el => ['input', 'change'].forEach(evt => el.addEventListener(evt, () => {
            clearTimeout(quoteTimer);
            quoteTimer = setTimeout(refreshQuote, 200);
        })));

        // Direct berekenen als beide velden al gevuld zijn (bv. na validatiefout met old-values)
        refreshQuote();

        couponBtn.addEventListener('click', async () => {
            const code = (couponEl.value || '').trim();
            if (!code) return;
            if (!startEl.value || !endEl.value) {
                couponMsg.textContent = 'Vul eerst start- en eindtijd in.';
                couponMsg.className = 'mt-1 text-xs font-semibold text-amber-600';
                couponMsg.classList.remove('hidden');
                return;
            }
            couponBtn.disabled = true;
            try {
                const res = await fetch('{{ route('technician.coupon.check') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ klantnummer, start_time: startEl.value, end_time: endEl.value, coupon_code: code }),
                });
                const d = await res.json().catch(() => ({}));
                if (res.ok && d.success) {
                    appliedCoupon = code.toUpperCase();
                    couponMsg.textContent = 'Kortingscode toegepast: −' + eur(d.discount);
                    couponMsg.className = 'mt-1 text-xs font-semibold text-green-600';
                    paintCoupon(d);
                    document.getElementById('prTotal').textContent = eur(d.total);
                    document.getElementById('prBtw').textContent = eur(d.btw);
                    document.getElementById('prSub').textContent = eur(d.subtotal);
                } else {
                    appliedCoupon = '';
                    couponMsg.textContent = d.error || 'Ongeldige kortingscode.';
                    couponMsg.className = 'mt-1 text-xs font-semibold text-red-600';
                    paintCoupon({ discount: 0 });
                    refreshQuote();
                }
                couponMsg.classList.remove('hidden');
            } finally {
                couponBtn.disabled = false;
            }
        });

        function clearErrors() {
            errBox.classList.add('hidden');
            errBox.textContent = '';
            form.querySelectorAll('.field-error').forEach(el => el.remove());
            form.querySelectorAll('input, select, textarea').forEach(el => el.classList.remove('!border-red-500'));
        }

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Zoals /afspraak: eerst native browser-validatie (rode ballonnetjes), daarna server-errors inline.
            if (!form.reportValidity()) return;

            clearErrors();
            btn.disabled = true;
            if (btnLabel) btnLabel.textContent = 'Bezig met verwerken…';
            const fd = new FormData(form);
            const payload = Object.fromEntries(fd.entries());
            try {
                const res = await fetch(form.action, {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(payload),
                });
                const data = await res.json().catch(() => ({}));
                if ((res.ok || res.status === 201) && data.redirect) {
                    window.location.href = data.redirect;
                    return;
                }
                if (res.status === 422 && data.errors) {
                    const firstField = Object.keys(data.errors)[0];
                    const firstMsg = data.errors[firstField]?.[0] || 'Controleer de gemarkeerde velden.';
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        if (!input) return;
                        input.classList.add('!border-red-500');
                        const err = document.createElement('p');
                        err.className = 'field-error mt-1 text-xs font-semibold text-red-600';
                        err.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                                        const anchor = input.type === 'checkbox' ? input.closest('label') : input;
                                        anchor.insertAdjacentElement('afterend', err);
                    });
                    const firstInput = firstField ? form.querySelector(`[name="${firstField}"]`) : null;
                    if (firstInput) {
                        firstInput.focus({ preventScroll: true });
                        firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    errBox.textContent = firstMsg;
                    errBox.classList.remove('hidden');
                    throw new Error(firstMsg);
                }
                if (res.status === 419) throw new Error('Sessie verlopen. Vernieuw de pagina en probeer het opnieuw.');
                throw new Error(data.message || 'Er ging iets mis. Probeer het opnieuw.');
            } catch (err) {
                if (err.message && !document.querySelector('.field-error')) {
                    errBox.textContent = err.message;
                    errBox.classList.remove('hidden');
                }
            } finally {
                btn.disabled = false;
                if (btnLabel) btnLabel.textContent = 'Verder naar betaling';
            }
        });
    })();
    </script>
    <script>
    // Onafhankelijke fallback-berekening (tweede pad naast het hoofdscript hierboven).
    window.techQuoteFallback = function () {
        try {
            var box = document.getElementById('priceBox');
            var sEl = document.getElementById('start_time');
            var eEl = document.getElementById('end_time');
            if (!box || !sEl || !eEl || !sEl.value || !eEl.value) return;
            var sp = sEl.value.split(':');
            var ep = eEl.value.split(':');
            var minutes = (parseInt(ep[0], 10) * 60 + parseInt(ep[1], 10)) - (parseInt(sp[0], 10) * 60 + parseInt(sp[1], 10));
            var hint = document.getElementById('prHint');
            if (!(minutes >= 5)) {
                if (hint) hint.textContent = 'De eindtijd moet na de starttijd zijn (minimaal 5 minuten).';
                return;
            }
            var qp = parseFloat(box.getAttribute('data-quarter')) || 0;
            var travel = box.getAttribute('data-freetravel') === '1' ? 0 : (parseFloat(box.getAttribute('data-travel')) || 0);
            var quarters = Math.ceil(minutes / 15);
            var bruto = Math.round((quarters * qp + travel) * 100) / 100;
            var dtype = box.getAttribute('data-dtype') || 'none';
            var dval = parseFloat(box.getAttribute('data-dval')) || 0;
            var md = 0;
            if (dtype === 'percent' && dval > 0) md = Math.round(bruto * dval / 100 * 100) / 100;
            else if (dtype === 'fixed' && dval > 0) md = Math.min(dval, bruto);
            var net = Math.round((bruto - md) * 100) / 100;
            var btw = Math.round(net * 21 / 121 * 100) / 100;
            var subtotal = Math.round((net - btw) * 100) / 100;
            var eur = function (v) { return '€ ' + Number(v || 0).toFixed(2).replace('.', ','); };
            document.getElementById('prAantal').textContent = Math.floor(minutes / 60) + 'uur ' + (minutes % 60) + 'min';
            document.getElementById('prTravel').textContent = eur(travel);
            var mRow = document.getElementById('prMemberRow');
            if (md > 0) {
                mRow.classList.remove('hidden'); mRow.classList.add('flex');
                document.getElementById('prMember').textContent = '−' + eur(md);
            }
            document.getElementById('prSub').textContent = eur(subtotal);
            document.getElementById('prTotal').textContent = eur(net);
            document.getElementById('prBtw').textContent = eur(btw);
            if (hint) hint.textContent = '';
        } catch (err) { /* stil */ }
    };
    </script>
@endsection
