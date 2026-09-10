<x-admin.layout title="Bestelling {{ $order->order_number }}">
    @php
        $statusPills = [
            'pending' => ['Nieuw', 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400', 'bg-amber-500'],
            'processing' => ['In behandeling', 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400', 'bg-blue-500'],
            'shipped' => ['Verzonden', 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400', 'bg-indigo-500'],
            'completed' => ['Afgerond', 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400', 'bg-emerald-500'],
            'cancelled' => ['Geannuleerd', 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400', 'bg-red-500'],
        ];
        $sp = $statusPills[$order->order_status] ?? [$order->order_status, 'bg-slate-100 text-slate-500', 'bg-slate-500'];
    @endphp
    <div class="w-full">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base font-extrabold tracking-tight sm:text-lg" style="color: var(--c-heading)">Bestelling {{ $order->order_number }}</h2>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $sp[1] }}"><span class="h-1.5 w-1.5 rounded-full {{ $sp[2] }}"></span>{{ $sp[0] }}</span>
                    @if($order->payment_status === 'paid')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Betaald</span>
                    @elseif($order->payment_status === 'failed')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-bold text-red-600 dark:bg-red-900/30 dark:text-red-400"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Mislukt</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-bold text-amber-600 dark:bg-amber-900/30 dark:text-amber-400"><span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>Open</span>
                    @endif
                </div>
                <p class="mt-0.5 text-xs" style="color: var(--c-muted)">{{ $order->created_at?->format('d-m-Y H:i') }} · {{ $order->customer_email }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-bold transition hover:bg-slate-50" style="color: var(--c-heading); border-color: var(--c-input-border)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Overzicht
            </a>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            <div class="xl:col-span-2 space-y-4">
                <div class="rounded-2xl border p-4 sm:p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
                    <h3 class="text-sm font-extrabold" style="color: var(--c-heading)">Producten</h3>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full border-collapse text-left" style="min-width: 560px">
                            <thead><tr class="border-b" style="border-color: rgba(148,163,184,.15)">
                                <th class="py-3 pr-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Product</th>
                                <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Prijs</th>
                                <th class="px-3 py-3 text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Aantal</th>
                                <th class="py-3 pl-3 text-end text-xs font-bold uppercase tracking-wide whitespace-nowrap" style="color: var(--c-muted)">Totaal</th>
                            </tr></thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr class="border-b transition hover:bg-blue-50/40 dark:hover:bg-slate-800/40" style="border-color: rgba(148,163,184,.12)">
                                        <td class="py-2.5 pr-3 text-sm font-semibold" style="color: var(--c-heading)">{{ $item->product_name }}</td>
                                        <td class="px-3 py-2.5 text-sm whitespace-nowrap">€{{ number_format($item->product_price, 2, ',', '.') }}</td>
                                        <td class="px-3 py-2.5 text-sm">{{ $item->quantity }}</td>
                                        <td class="py-2.5 pl-3 text-end text-sm font-bold whitespace-nowrap" style="color: var(--c-heading)">€{{ number_format($item->total_price, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 space-y-1.5 text-sm" style="color: var(--c-body)">
                        <div class="flex justify-between"><span>Subtotaal (excl. btw)</span><span>€{{ number_format($order->subtotal, 2, ',', '.') }}</span></div>
                        @if((float) $order->discount_amount > 0)
                            <div class="flex justify-between text-emerald-600"><span>Korting {{ $order->discount_code ? '(' . $order->discount_code . ')' : '' }}</span><span>−€{{ number_format($order->discount_amount, 2, ',', '.') }}</span></div>
                        @endif
                        <div class="flex justify-between"><span>Verzending ({{ $order->shipping_method === 'pickup' ? 'afhalen' : 'verzending' }})</span><span>@if((float) $order->shipping_cost > 0) €{{ number_format($order->shipping_cost, 2, ',', '.') }} @else Gratis @endif</span></div>
                        <div class="flex justify-between"><span>BTW ({{ number_format($order->tax_percentage, 0) }}% incl.)</span><span>€{{ number_format($order->tax_amount, 2, ',', '.') }}</span></div>
                        <div class="flex justify-between border-t pt-2.5 text-base font-extrabold" style="color: var(--c-heading); border-color: rgba(148,163,184,.15)"><span>Totaal</span><span>€{{ number_format($order->total_price, 2, ',', '.') }}</span></div>
                    </div>
                </div>

                <div class="rounded-2xl border p-4 sm:p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
                    <h3 class="text-sm font-extrabold" style="color: var(--c-heading)">Adres</h3>
                    <p class="mt-2 text-sm leading-6" style="color: var(--c-body)">
                        <strong>{{ $order->billingAddress?->fullName() }}</strong><br>
                        {{ $order->billingAddress?->fullStreet() }}<br>
                        {{ $order->billingAddress?->postcode }} {{ $order->billingAddress?->city }}<br>
                        {{ $order->customer_phone }}
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-2xl border p-4 sm:p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
                    <h3 class="text-sm font-extrabold" style="color: var(--c-heading)">Status</h3>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between"><span style="color: var(--c-muted)">Betaling</span><strong>{{ $order->payment_status === 'paid' ? 'Betaald' : ($order->payment_status === 'failed' ? 'Mislukt' : 'Open') }}</strong></div>
                        <div class="flex justify-between"><span style="color: var(--c-muted)">Methode</span><strong class="capitalize">{{ $order->payment_method ?? '—' }}</strong></div>
                        <div class="flex justify-between"><span style="color: var(--c-muted)">Klantnummer</span><strong>{{ $order->klantnummer ?? '—' }}</strong></div>
                    </div>
                    <label class="mt-4 block text-xs font-bold uppercase tracking-wide" style="color: var(--c-muted)">Bestelstatus wijzigen</label>
                    <select id="orderStatusSelect" class="mt-1.5 h-10 w-full rounded-lg border px-2 text-sm outline-none" style="background-color: var(--c-input-bg); border-color: var(--c-input-border); color: var(--c-heading)">
                        @foreach(['pending' => 'Nieuw', 'processing' => 'In behandeling', 'shipped' => 'Verzonden', 'completed' => 'Afgerond', 'cancelled' => 'Geannuleerd'] as $v => $l)
                            <option value="{{ $v }}" {{ $order->order_status === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <p id="orderStatusMsg" class="mt-2 hidden text-xs font-bold"></p>
                </div>

                <div class="rounded-2xl border p-4 sm:p-6" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
                    <h3 class="text-sm font-extrabold" style="color: var(--c-heading)">Factuur</h3>
                    @if($order->invoice)
                        <p class="mt-2 text-sm" style="color: var(--c-body)">{{ $order->invoice->invoice_number }} · €{{ number_format($order->invoice->total, 2, ',', '.') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" data-invoice-download="{{ route('admin.orders.invoice', $order) }}" data-filename="{{ $order->invoice->invoice_number }}.pdf" class="inline-flex h-10 items-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60">
                                <svg class="dl-spinner hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <svg class="dl-icon h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                <span class="dl-label">PDF downloaden</span>
                            </button>
                        </div>
                    @else
                        <p class="mt-2 text-sm" style="color: var(--c-muted)">Nog geen factuur (wordt aangemaakt na betaling).</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const sel = document.getElementById('orderStatusSelect');
            const msg = document.getElementById('orderStatusMsg');
            const token = document.querySelector('meta[name=csrf-token]')?.content || '';
            sel.addEventListener('change', async () => {
                msg.classList.add('hidden');
                const res = await fetch('{{ route('admin.orders.status', $order) }}', {
                    method: 'POST', credentials: 'same-origin',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_status: sel.value })
                });
                const d = await res.json().catch(() => ({}));
                msg.textContent = res.ok ? ('✓ ' + (d.message || 'Bijgewerkt.')) : ('✗ ' + (d.message || 'Mislukt.'));
                msg.className = 'mt-2 text-xs font-bold ' + (res.ok ? 'text-emerald-600' : 'text-red-600');
            });

            /* Factuur downloaden met loading-state (zoals hardware-facturen).
               De ?t= cache-buster zorgt dat de browser nooit een eerder
               gecachte (oude) PDF-response voor deze URL kan tonen. */
            document.querySelectorAll('[data-invoice-download]').forEach((btn) => {
                btn.addEventListener('click', async () => {
                    const spinner = btn.querySelector('.dl-spinner');
                    const icon = btn.querySelector('.dl-icon');
                    const label = btn.querySelector('.dl-label');
                    const origLabel = label ? label.textContent : '';
                    btn.disabled = true;
                    if (spinner) spinner.classList.remove('hidden');
                    if (icon) icon.classList.add('hidden');
                    if (label) label.textContent = 'Bezig...';
                    try {
                        const requestUrl = btn.dataset.invoiceDownload + (btn.dataset.invoiceDownload.includes('?') ? '&' : '?') + 't=' + Date.now();
                        const res = await fetch(requestUrl, { credentials: 'same-origin', cache: 'no-store' });
                        if (!res.ok) throw new Error('Download mislukt.');
                        const blob = await res.blob();
                        let filename = btn.dataset.filename || 'factuur.pdf';
                        const disp = res.headers.get('Content-Disposition') || '';
                        const m = disp.match(/filename="?([^";]+)"?/);
                        if (m) filename = m[1];
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        setTimeout(() => URL.revokeObjectURL(url), 5000);
                    } catch (e) {
                        if (label) label.textContent = '✗ ' + (e.message || 'Mislukt.');
                        await new Promise((r) => setTimeout(r, 2000));
                    } finally {
                        btn.disabled = false;
                        if (spinner) spinner.classList.add('hidden');
                        if (icon) icon.classList.remove('hidden');
                        if (label) label.textContent = origLabel;
                    }
                });
            });
        })();
    </script>
</x-admin.layout>
