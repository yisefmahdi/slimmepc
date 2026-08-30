<x-admin.layout title="Nieuwe ontvangst — {{ ['laptop' => 'Laptops-PC', 'ipad_iphone' => 'iPad-iPhone', 'playstation_xbox' => 'PlayStation-Xbox'][$type] ?? $type }}">
    @php
        $typeLabel = ['laptop' => 'Laptops-PC', 'ipad_iphone' => 'iPad-iPhone', 'playstation_xbox' => 'PlayStation-Xbox'][$type] ?? $type;
        $typeParam = $type;
    @endphp
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold tracking-tight" style="color: var(--c-heading)">Handmatig apparaat ontvangen — {{ $typeLabel }}</h2>
                <p class="mt-1 text-sm" style="color: var(--c-muted)">Vul de gegevens in. Er wordt een bevestigingsmail naar de klant verzonden.</p>
            </div>
            <a href="{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => $typeParam]) }}" class="inline-flex h-10 items-center gap-2 rounded-xl border px-4 text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800" style="color: var(--c-heading); border-color: var(--c-input-border)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                Overzicht
            </a>
        </div>

        <form id="ontvangstForm" class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            @csrf
            <input type="hidden" name="type" value="{{ $typeParam }}">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        Naam klant *
                    </label>
                    <input type="text" name="customer_name" required placeholder="Bijv. Yousef Ziad Mahdi"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        E-mailadres *
                    </label>
                    <input type="email" name="customer_email" required placeholder="klant@voorbeeld.nl"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75V6.75m0 0c1.5 0 2.25 1.5 2.25 3s-1.5 3-2.25 3m0-6c-1.5 0-2.25 1.5-2.25 3s1.5 3 2.25 3m2.25-3h9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Telefoonnummer *
                    </label>
                    <input type="text" name="phone_number" required placeholder="Bijv. 0612345678"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div>
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879.879l-4.5-4.5a3 3 0 01.879-.879V12a3 3 0 013-3h6a3 3 0 013 3v1.757a3 3 0 01-.879.879l-4.5 4.5a3 3 0 01-.879-.879V17.25M9 17.25a3 3 0 003 3h0a3 3 0 003-3M9 17.25h6" /></svg>
                        Type apparaat *
                    </label>
                    <input type="text" name="device_type" required placeholder="Bijv. HP, iPhone 15, PS5"
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.25a3 3 0 013-3h6a3 3 0 013 3v6a3 3 0 01-3 3h-6a3 3 0 01-3-3v-6zM9 9h6" /></svg>
                        Serienummer
                    </label>
                    <input type="text" name="serial_number" placeholder="Bijv. SN123456"
                           class="form-input h-11 w-full text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                        Opmerkingen
                    </label>
                    <textarea name="notes" rows="4" placeholder="Extra informatie..."
                              class="form-input w-full py-3 text-sm"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        Datum &amp; tijd van ontvangst *
                    </label>
                    <input type="datetime-local" name="received_at" required
                           class="form-input h-11 w-full text-sm">
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600"></p>
                </div>
            </div>

            <button type="submit" id="ontvangstSubmitBtn"
                    class="mt-8 inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 text-sm font-bold text-white shadow-[0_10px_25px_rgba(37,99,235,.25)] transition hover:-translate-y-0.5 hover:bg-blue-700 disabled:opacity-60">
                <svg id="ontvangstSubmitSpinner" class="hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span id="ontvangstSubmitLabel">Apparaat toevoegen &amp; bevestigen</span>
            </button>
            <div id="ontvangstFormMsg" class="mt-4 hidden rounded-xl border px-4 py-3 text-sm font-bold"></div>
        </form>
    </div>

    <script>
        (function(){
            const form = document.getElementById('ontvangstForm');
            const btn = document.getElementById('ontvangstSubmitBtn');
            const spinner = document.getElementById('ontvangstSubmitSpinner');
            const label = document.getElementById('ontvangstSubmitLabel');
            const msg = document.getElementById('ontvangstFormMsg');

            // default received_at to now
            const dt = form.querySelector('input[name=received_at]');
            if (dt && !dt.value) {
                const now = new Date();
                const pad = n => String(n).padStart(2,'0');
                dt.value = now.getFullYear()+'-'+pad(now.getMonth()+1)+'-'+pad(now.getDate())+'T'+pad(now.getHours())+':'+pad(now.getMinutes());
            }

            form.addEventListener('submit', async (e)=>{
                e.preventDefault();
                if(!form.reportValidity()) return;
                btn.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Bezig met verzenden…';
                msg.className = 'mt-4 hidden text-sm font-semibold';
                msg.textContent = '';
                form.querySelectorAll('.field-error').forEach(el=>{ el.textContent=''; el.classList.add('hidden'); });
                form.querySelectorAll('.form-input').forEach(el=> el.classList.remove('!border-red-500'));

                const fd = new FormData(form);
                const payload = Object.fromEntries(fd.entries());

                try{
                    const res = await fetch('{{ route('admin.bevestiging-mail.ontvangst.store') }}', {
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
                    if(res.ok && data.receipt){
                        msg.textContent = '✓ ' + (data.message || 'Ontvangst succesvol aangemaakt en verzonden!');
                        msg.className = 'mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700';
                        msg.classList.remove('hidden');
                        form.reset();
                        // re-apply default datetime
                        if (dt) {
                            const now2 = new Date();
                            const pad2 = n => String(n).padStart(2,'0');
                            dt.value = now2.getFullYear()+'-'+pad2(now2.getMonth()+1)+'-'+pad2(now2.getDate())+'T'+pad2(now2.getHours())+':'+pad2(now2.getMinutes());
                        }
                        setTimeout(()=>{ window.location.href = '{{ route('admin.bevestiging-mail.ontvangst.index', ['type' => $typeParam]) }}'; }, 1500);
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
                        msg.textContent = '✗ ' + firstErr;
                        msg.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700';
                        msg.classList.remove('hidden');
                        return;
                    }
                    throw new Error(data.message || 'Er ging iets mis.');
                }catch(err){
                    msg.textContent = '✗ ' + (err.message || 'Er ging iets mis.');
                    msg.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700';
                    msg.classList.remove('hidden');
                }finally{
                    btn.disabled = false;
                    spinner.classList.add('hidden');
                    label.textContent = 'Apparaat toevoegen & bevestigen';
                }
            });
        })();
    </script>
</x-admin.layout>
