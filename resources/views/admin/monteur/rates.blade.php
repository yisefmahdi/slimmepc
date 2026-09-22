<x-admin.layout title="Monteur tarieven">
    <div class="mx-auto w-full max-w-2xl">
        <div class="mb-4">
            <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Monteur tarieven</h2>
            <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Uurtarief, voorrijkosten en kortingen voor leden. Prijzen zijn incl. btw.</p>
        </div>

        <form id="monRatesForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Uurtarief (€, incl. btw) *</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">€</span>
                        <input type="number" id="rateHour" min="1" max="9999" step="0.01" required
                               value="{{ number_format((float) $hour_price, 2, '.', '') }}"
                               class="form-input h-12 w-full pl-9 text-base font-bold">
                    </div>
                    <p class="mt-1 text-[11px]" style="color: var(--c-muted)">Per kwartier: €<span id="rateQuarterPreview">{{ number_format((float) $hour_price / 4, 2, ',', '.') }}</span></p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Voorrijkosten (€, incl. btw) *</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-slate-400">€</span>
                        <input type="number" id="rateTravel" min="0" max="9999" step="0.01" required
                               value="{{ number_format((float) $travel_cost, 2, '.', '') }}"
                               class="form-input h-12 w-full pl-9 text-base font-bold">
                    </div>
                </div>
            </div>

            <div class="mt-6 border-t pt-6" style="border-color: rgba(148,163,184,.15)">
                <h3 class="text-sm font-extrabold" style="color: var(--c-heading)">Lidmaatschapskorting</h3>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">Wordt toegepast als de klant een actief lidmaatschap heeft (check op e-mail).</p>

                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Type korting</label>
                        <select id="rateDiscType"
                                class="h-12 w-full cursor-pointer appearance-none rounded-xl border px-4 text-sm font-semibold outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100 dark:focus:ring-blue-900/40"
                                style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading); background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='m19.5 8.25-7.5 7.5-7.5-7.5'/%3E%3C/svg%3E&quot;); background-repeat: no-repeat; background-position: right 1rem center; background-size: 14px;">
                            <option value="none" {{ $member_discount_type === 'none' ? 'selected' : '' }}>Geen korting</option>
                            <option value="percent" {{ $member_discount_type === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                            <option value="fixed" {{ $member_discount_type === 'fixed' ? 'selected' : '' }}>Vast bedrag (€)</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">Waarde <span id="rateDiscUnit">(% of €)</span></label>
                        <input type="number" id="rateDiscValue" min="0" max="9999" step="0.01"
                               value="{{ number_format((float) $member_discount_value, 2, '.', '') }}"
                               class="form-input h-12 w-full text-base font-bold">
                    </div>
                </div>

                <label class="mt-4 flex cursor-pointer items-center gap-3 rounded-xl border px-4 py-3 transition hover:bg-blue-50/50 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.2)">
                    <input type="checkbox" id="rateFreeTravel" {{ $member_free_travel ? 'checked' : '' }}
                           class="h-5 w-5 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-200">
                    <span class="text-sm font-semibold" style="color: var(--c-heading)">Leden betalen geen voorrijkosten
                        <span class="block text-xs font-normal" style="color: var(--c-muted)">Reiskosten worden dan automatisch op €0 gezet voor leden.</span>
                    </span>
                </label>
            </div>

            <p id="monRatesError" class="mt-4 hidden text-xs font-semibold text-red-600"></p>

            <button type="submit" id="monRatesBtn" data-loading
                    class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#075be8] to-[#064bd7] px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(0,91,234,0.25)] transition duration-300 hover:-translate-y-0.5 disabled:opacity-60">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                <span>Tarieven opslaan</span>
            </button>
        </form>
    </div>

    <script>
        (function () {
            const form = document.getElementById('monRatesForm');
            if (!form) return;
            const hourEl = document.getElementById('rateHour');
            const travelEl = document.getElementById('rateTravel');
            const typeEl = document.getElementById('rateDiscType');
            const valueEl = document.getElementById('rateDiscValue');
            const freeEl = document.getElementById('rateFreeTravel');
            const unitEl = document.getElementById('rateDiscUnit');
            const quarterPreview = document.getElementById('rateQuarterPreview');
            const err = document.getElementById('monRatesError');
            const btn = document.getElementById('monRatesBtn');
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            function toast(msg, type) {
                if (window.SlimmePC && window.SlimmePC.toast) {
                    if (type === 'success') window.SlimmePC.toast.success(msg);
                    else window.SlimmePC.toast.error(msg);
                }
            }
            function paintUnit() {
                unitEl.textContent = typeEl.value === 'percent' ? '(%)' : (typeEl.value === 'fixed' ? '(€)' : '(% of €)');
                valueEl.disabled = typeEl.value === 'none';
                valueEl.classList.toggle('opacity-50', typeEl.value === 'none');
            }
            hourEl.addEventListener('input', () => {
                quarterPreview.textContent = '€' + (Number(hourEl.value || 0) / 4).toFixed(2).replace('.', ',');
            });
            typeEl.addEventListener('change', paintUnit);
            paintUnit();

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                err.classList.add('hidden');
                btn.disabled = true;
                try {
                    const res = await fetch('{{ route('admin.monteur.rates.update') }}', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({
                            hour_price: hourEl.value,
                            travel_cost: travelEl.value,
                            member_discount_type: typeEl.value,
                            member_discount_value: valueEl.value || 0,
                            member_free_travel: freeEl.checked,
                        }),
                    });
                    const d = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        const firstErr = d.errors ? Object.values(d.errors)[0]?.[0] : null;
                        const msg = firstErr || d.message || 'Opslaan mislukt.';
                        err.textContent = msg;
                        err.classList.remove('hidden');
                        throw new Error(msg);
                    }
                    toast(d.message || 'Opgeslagen.', 'success');
                } catch (e2) {
                    if (err.classList.contains('hidden')) toast(e2.message || 'Opslaan mislukt.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });
        })();
    </script>
</x-admin.layout>
