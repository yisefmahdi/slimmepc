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
                        <h1 class="text-3xl font-bold tracking-tight text-[#071b46] sm:text-[34px]">Betaling klant</h1>
                        <p class="mt-2 text-sm text-slate-500 sm:text-[15px]">{{ $client->name }} · {{ $client->klantnummer }}</p>
                        @if($pricing['is_member'])
                            <p class="mx-auto mt-3 inline-flex items-center gap-1.5 rounded-full bg-green-50 px-4 py-1.5 text-xs font-bold text-green-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                Lid — korting wordt automatisch toegepast
                            </p>
                        @endif
                    </div>

                    <div id="techFormError" class="mb-5 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700"></div>

                    <form id="techForm" action="{{ route('technician.payment.store') }}" method="POST" novalidate>
                        @csrf
                        <input type="hidden" name="klantnummer" value="{{ $client->klantnummer }}">

                        <div class="grid grid-cols-1 gap-x-7 gap-y-5 md:grid-cols-2">
                            <div>
                                <label for="start_time" class="mb-1.5 block text-sm font-medium text-[#071b46]">Starttijd *</label>
                                <input id="start_time" name="start_time" type="time" required value="{{ old('start_time') }}"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-[#071b46] outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                @error('start_time')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_time" class="mb-1.5 block text-sm font-medium text-[#071b46]">Eindtijd *</label>
                                <input id="end_time" name="end_time" type="time" required value="{{ old('end_time') }}"
                                    class="h-[46px] w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-[#071b46] outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                @error('end_time')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="description" class="mb-1.5 block text-sm font-medium text-[#071b46]">Omschrijving</label>
                                <textarea id="description" name="description" rows="2" placeholder="Korte omschrijving van het bezoek..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('description') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="work_done" class="mb-1.5 block text-sm font-medium text-[#071b46]">Uitgevoerde werkzaamheden</label>
                                <textarea id="work_done" name="work_done" rows="2" placeholder="Wat is er gedaan?"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('work_done') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label for="advice" class="mb-1.5 block text-sm font-medium text-[#071b46]">Advies</label>
                                <textarea id="advice" name="advice" rows="2" placeholder="Advies voor de klant..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('advice') }}</textarea>
                            </div>
                            <div>
                                <label for="rating" class="mb-1.5 block text-sm font-medium text-[#071b46]">Beoordeling (1–5)</label>
                                <select id="rating" name="rating"
                                    class="h-[46px] w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-600 outline-none transition hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                                    <option value="">Geen beoordeling</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ (string) old('rating') === (string) $i ? 'selected' : '' }}>{{ $i }} {{ $i === 1 ? 'ster' : 'sterren' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="coupon_code" class="mb-1.5 block text-sm font-medium text-[#071b46]">Kortingscode</label>
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
                            <div class="md:col-span-2">
                                <label for="comment" class="mb-1.5 block text-sm font-medium text-[#071b46]">Opmerking</label>
                                <textarea id="comment" name="comment" rows="2" placeholder="Extra opmerking..."
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-[#071b46] outline-none transition placeholder:text-slate-400 hover:border-blue-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-100">{{ old('comment') }}</textarea>
                            </div>
                        </div>

                        <button id="techSubmitBtn" type="submit"
                            class="mt-6 flex h-[54px] w-full items-center justify-center gap-2.5 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] text-base font-semibold text-white shadow-[0_12px_28px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_16px_32px_rgba(0,91,234,0.32)] disabled:opacity-60">
                            <span id="techSubmitLabel">Verder naar betaling</span>
                        </button>
                    </form>
                </div>

                <!-- Prijsopbouw -->
                <aside class="w-full rounded-[28px] border border-white/80 bg-white/95 px-6 py-7 shadow-[0_25px_80px_rgba(37,99,235,0.14)] backdrop-blur-xl sm:px-7 lg:w-[300px] lg:shrink-0">
                    <h2 class="text-lg font-bold text-[#071b46]">Prijsopbouw</h2>
                    <p class="mt-1 text-xs text-slate-500">Tarief €{{ number_format($pricing['hour_price'], 2, ',', '.') }}/uur · per kwartier afgerekend</p>
                    <dl id="priceRows" class="mt-4 space-y-2.5 text-sm">
                        <div class="flex items-center justify-between gap-3"><dt class="text-slate-500">Arbeid</dt><dd id="prLabor" class="font-bold text-[#071b46]">—</dd></div>
                        <div class="flex items-center justify-between gap-3"><dt class="text-slate-500">Voorrijkosten</dt><dd id="prTravel" class="font-bold text-[#071b46]">—</dd></div>
                        <div id="prMemberRow" class="hidden items-center justify-between gap-3"><dt class="font-semibold text-green-700">Lidkorting</dt><dd id="prMember" class="font-bold text-green-700">—</dd></div>
                        <div id="prCouponRow" class="hidden items-center justify-between gap-3"><dt class="font-semibold text-green-700">Kortingscode</dt><dd id="prCoupon" class="font-bold text-green-700">—</dd></div>
                        <div class="flex items-center justify-between gap-3"><dt class="text-slate-500">BTW (21%)</dt><dd id="prBtw" class="font-bold text-[#071b46]">—</dd></div>
                        <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3"><dt class="font-bold text-[#071b46]">Totaal (incl. btw)</dt><dd id="prTotal" class="text-lg font-black text-blue-700">—</dd></div>
                        <p id="prHint" class="text-xs text-slate-400">Vul start- en eindtijd in voor een berekening.</p>
                    </dl>
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

        const eur = v => '€' + Number(v || 0).toFixed(2).replace('.', ',');

        function paintQuote(d) {
            document.getElementById('prLabor').textContent = d.quarters + ' × ' + eur(d.quarter_price);
            document.getElementById('prTravel').textContent = Number(d.travel) > 0 ? eur(d.travel) : 'Gratis';
            const mRow = document.getElementById('prMemberRow');
            if (Number(d.member_discount) > 0) {
                mRow.classList.remove('hidden'); mRow.classList.add('flex');
                document.getElementById('prMember').textContent = '−' + eur(d.member_discount);
            } else { mRow.classList.add('hidden'); mRow.classList.remove('flex'); }
            document.getElementById('prTotal').textContent = eur(d.total);
            document.getElementById('prBtw').textContent = eur(d.btw);
            document.getElementById('prHint').textContent = d.minutes + ' minuten' + (d.is_member ? ' · lidkorting toegepast' : '');
        }

        function paintCoupon(d) {
            const cRow = document.getElementById('prCouponRow');
            if (Number(d.discount) > 0) {
                cRow.classList.remove('hidden'); cRow.classList.add('flex');
                document.getElementById('prCoupon').textContent = '−' + eur(d.discount);
            } else { cRow.classList.add('hidden'); cRow.classList.remove('flex'); }
            document.getElementById('prTotal').textContent = eur(d.total);
        }

        async function refreshQuote() {
            if (!startEl.value || !endEl.value) return;
            try {
                const res = await fetch('{{ route('technician.quote') }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ klantnummer, start_time: startEl.value, end_time: endEl.value }),
                });
                const d = await res.json().catch(() => ({}));
                if (res.ok && d.success) { paintQuote(d); appliedCoupon = ''; paintCoupon({ discount: 0, total: d.total }); }
            } catch (e) { /* stil */ }
        }

        [startEl, endEl].forEach(el => el.addEventListener('change', () => {
            clearTimeout(quoteTimer);
            quoteTimer = setTimeout(refreshQuote, 400);
        }));

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
@endsection
