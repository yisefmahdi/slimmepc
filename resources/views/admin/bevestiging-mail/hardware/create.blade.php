<x-admin.layout title="Nieuwe factuur — Hardware">
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold tracking-tight" style="color: var(--c-heading)">Handmatig Factuur Aanmaken</h2>
                <p class="mt-1 text-sm" style="color: var(--c-muted)">Vul de gegevens in. De factuur wordt als PDF gegenereerd en per e-mail verzonden.</p>
            </div>
            <a href="{{ route('admin.bevestiging-mail.hardware.index') }}" class="inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800" style="color: var(--c-heading); border-color: var(--c-input-border)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Overzicht
            </a>
        </div>

        <form id="hardwareInvoiceForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            @csrf
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">👤 Naam klant *</label>
                    <input type="text" name="name" required placeholder="Bijv. Yousef Ziad Mahdi"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">📧 E-mailadres *</label>
                    <input type="email" name="email" required placeholder="klant@voorbeeld.nl"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">💻 Apparaat info</label>
                    <input type="text" name="device_info" placeholder="Bijv. Gaming-pc, HP Laptop"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">🛠️ Probleembeschrijving</label>
                    <textarea name="description" rows="4" placeholder="Omschrijving van werkzaamheden..."
                              class="form-input w-full py-3 text-sm"></textarea>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">💶 Subtotaal (€) *</label>
                    <input type="number" name="subtotal" id="subtotal" required min="0" step="0.01" placeholder="Bijv. 330.58"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">💼 BTW (%) *</label>
                    <input type="number" name="tax_percentage" id="tax_percentage" required min="0" max="100" step="1" value="21"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 block text-sm font-semibold" style="color: var(--c-heading)">🧾 Totaal (€)</label>
                    <input type="text" id="total_display" readonly
                           class="form-input h-11 w-full text-sm font-bold" style="background-color: var(--c-page)" placeholder="Bijv. 150.00">
                    <p class="mt-1 text-xs" style="color: var(--c-muted)">Wordt automatisch berekend: Totaal = Subtotaal + BTW</p>
                </div>
            </div>

            <button type="submit" id="hardwareSubmitBtn"
                    class="mt-8 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60">
                <svg id="hardwareSubmitSpinner" class="hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span id="hardwareSubmitLabel">💾 Factuur aanmaken &amp; verzenden</span>
            </button>
            <p id="hardwareFormMsg" class="mt-3 hidden text-sm font-semibold"></p>
        </form>
    </div>

    <script>
        (function(){
            const subtotalEl = document.getElementById('subtotal');
            const taxEl = document.getElementById('tax_percentage');
            const totalEl = document.getElementById('total_display');
            function recalc(){
                const sub = parseFloat(subtotalEl.value) || 0;
                const pct = parseInt(taxEl.value) || 0;
                const tax = Math.round(sub * pct) / 100;
                // keep 2 decimals without floating errors
                const taxFixed = (sub * pct / 100);
                const total = sub + taxFixed;
                totalEl.value = total.toFixed(2).replace('.', ',') + ' €';
                totalEl.dataset.raw = total.toFixed(2);
            }
            subtotalEl.addEventListener('input', recalc);
            taxEl.addEventListener('input', recalc);
            recalc();

            const form = document.getElementById('hardwareInvoiceForm');
            const btn = document.getElementById('hardwareSubmitBtn');
            const spinner = document.getElementById('hardwareSubmitSpinner');
            const label = document.getElementById('hardwareSubmitLabel');
            const msg = document.getElementById('hardwareFormMsg');

            form.addEventListener('submit', async (e)=>{
                e.preventDefault();
                if(!form.reportValidity()) return;
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Bezig met verzenden…';
                msg.className = 'mt-3 hidden text-sm font-semibold';
                msg.textContent = '';
                // clear previous errors
                form.querySelectorAll('.field-error').forEach(el=>{ el.textContent=''; el.classList.add('hidden'); });
                form.querySelectorAll('.form-input').forEach(el=> el.classList.remove('!border-red-500'));

                const fd = new FormData(form);
                const payload = Object.fromEntries(fd.entries());

                try{
                    const res = await fetch('{{ route('admin.bevestiging-mail.hardware.store') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || document.querySelector('input[name=_token]')?.value || '',
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json().catch(()=>({}));
                    if(res.ok && (data.invoice || data.message)){
                        msg.textContent = data.message || 'Factuur verzonden!';
                        msg.className = 'mt-3 text-sm font-semibold text-green-600';
                        msg.classList.remove('hidden');
                        form.reset();
                        taxEl.value = '21';
                        recalc();
                        setTimeout(()=>{ window.location.href = '{{ route('admin.bevestiging-mail.hardware.index') }}'; }, 1200);
                        return;
                    }
                    if(res.status===422 && data.errors){
                        Object.entries(data.errors).forEach(([field, msgs])=>{
                            const input = form.querySelector(`[name="${field}"]`);
                            if(input){
                                input.classList.add('!border-red-500');
                                const errEl = input.parentElement.querySelector('.field-error');
                                if(errEl){ errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
                            }
                        });
                        const firstErr = Object.values(data.errors)[0]?.[0] || 'Controleer de velden.';
                        msg.textContent = firstErr;
                        msg.className = 'mt-3 text-sm font-semibold text-red-600';
                        msg.classList.remove('hidden');
                        return;
                    }
                    throw new Error(data.message || 'Er ging iets mis.');
                }catch(err){
                    msg.textContent = err.message || 'Er ging iets mis.';
                    msg.className = 'mt-3 text-sm font-semibold text-red-600';
                    msg.classList.remove('hidden');
                }finally{
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    label.textContent = '💾 Factuur aanmaken & verzenden';
                }
            });
        })();
    </script>
</x-admin.layout>
