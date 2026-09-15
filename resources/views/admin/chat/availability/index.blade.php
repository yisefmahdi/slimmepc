<x-admin.layout title="Openingstijden — Live Chat">
    <div class="w-full max-w-4xl">
        <div class="mb-6">
            <h2 class="text-lg font-extrabold tracking-tight" style="color: var(--c-heading)">Openingstijden Live Chat</h2>
            <p class="mt-1 text-sm" style="color: var(--c-muted)">
                Bepaalt wanneer de chat open is. Buiten deze tijden ziet de bezoeker een offline-formulier per e-mail.
                {!! $status['open']
                    ? '<span class="font-bold text-green-600">● Nu open</span>'
                    : '<span class="font-bold text-red-600">● Nu gesloten</span> — '.e($status['reason'] ?? '') !!}
            </p>
        </div>

        <div class="rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <h3 class="mb-4 text-sm font-extrabold uppercase tracking-wide" style="color: var(--c-heading)">Wekelijkse tijden</h3>
            <div class="space-y-3">
                @foreach ($days as $day)
                    <div class="flex flex-col gap-2 rounded-xl border p-3 sm:flex-row sm:items-center sm:gap-4" style="border-color: rgba(148,163,184,.2)" data-day-row="{{ $day->id }}">
                        <label class="flex min-w-0 flex-1 cursor-pointer items-center gap-3">
                            <input type="checkbox" data-day-open {{ $day->is_open ? 'checked' : '' }} class="h-5 w-5 rounded accent-blue-600">
                            <span class="text-sm font-bold" style="color: var(--c-heading)">{{ \App\Models\ChatAvailability::dayName($day->day_of_week) }}</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="time" data-day-from value="{{ $day->open_at ? substr($day->open_at, 0, 5) : '09:00' }}"
                                   class="form-input h-10 w-28 text-sm" {{ $day->is_open ? '' : 'disabled' }}>
                            <span class="text-xs font-bold" style="color: var(--c-muted)">tot</span>
                            <input type="time" data-day-to value="{{ $day->close_at ? substr($day->close_at, 0, 5) : '17:00' }}"
                                   class="form-input h-10 w-28 text-sm" {{ $day->is_open ? '' : 'disabled' }}>
                            <button type="button" data-day-save="{{ $day->id }}"
                                    class="inline-flex h-10 items-center justify-center rounded-xl bg-blue-600 px-4 text-sm font-bold text-white hover:bg-blue-700 disabled:opacity-60">
                                Opslaan
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 rounded-2xl border p-6 sm:p-8" style="background-color: var(--c-card); border-color: rgba(148,163,184,.2); box-shadow: 0 14px 35px rgba(15,23,42,.06)">
            <h3 class="mb-1 text-sm font-extrabold uppercase tracking-wide" style="color: var(--c-heading)">Vrije dagen (feestdagen)</h3>
            <p class="mb-4 text-xs" style="color: var(--c-muted)">Op deze datums is de chat de hele dag gesloten.</p>
            <form id="closedDateForm" class="mb-4 flex flex-col gap-2 sm:flex-row">
                <input type="date" id="closedDateInput" required class="form-input h-11 flex-1 text-sm">
                <input type="text" id="closedReasonInput" maxlength="255" placeholder="Reden (optioneel, bijv. Koningsdag)"
                       class="form-input h-11 flex-[2] text-sm">
                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-xl bg-blue-600 px-5 text-sm font-bold text-white hover:bg-blue-700">
                    Toevoegen
                </button>
            </form>
            <div id="closedDatesList" class="space-y-2">
                @forelse ($closedDates as $d)
                    <div class="flex items-center justify-between gap-3 rounded-xl border px-4 py-2.5" style="border-color: rgba(148,163,184,.2)" data-closed-row="{{ $d->id }}">
                        <p class="text-sm font-semibold" style="color: var(--c-heading)">
                            {{ $d->closed_at->format('d-m-Y') }}
                            <span class="ml-2 text-xs font-normal" style="color: var(--c-muted)">{{ $d->reason }}</span>
                        </p>
                        <button type="button" data-closed-del="{{ $d->id }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" aria-label="Verwijderen">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @empty
                    <p class="text-xs" style="color: var(--c-muted)">Geen vrije dagen ingesteld.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        (function(){
            function token(){
                const m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.content : '';
            }
            function toast(msg, ok){
                if (window.SlimmePC && window.SlimmePC.toast) {
                    ok ? window.SlimmePC.toast.success(msg) : window.SlimmePC.toast.error(msg);
                } else if (!ok) alert(msg);
            }

            document.querySelectorAll('[data-day-row]').forEach(row => {
                const openBox = row.querySelector('[data-day-open]');
                const from = row.querySelector('[data-day-from]');
                const to = row.querySelector('[data-day-to]');
                const saveBtn = row.querySelector('[data-day-save]');
                openBox.addEventListener('change', () => {
                    from.disabled = !openBox.checked;
                    to.disabled = !openBox.checked;
                });
                saveBtn.addEventListener('click', async () => {
                    saveBtn.disabled = true;
                    try {
                        const res = await fetch('/admin/chat/beschikbaarheid/' + saveBtn.dataset.daySave, {
                            method: 'PUT',
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token(), 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ is_open: openBox.checked ? 1 : 0, open_at: from.value || null, close_at: to.value || null }),
                        });
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw new Error((data.errors && Object.values(data.errors)[0][0]) || data.message || 'Opslaan mislukt.');
                        toast(data.message || 'Opgeslagen.', true);
                    } catch (e) { toast(e.message, false); }
                    finally { saveBtn.disabled = false; }
                });
            });

            document.getElementById('closedDateForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                const date = document.getElementById('closedDateInput').value;
                const reason = document.getElementById('closedReasonInput').value.trim();
                if (!date) return;
                try {
                    const res = await fetch('/admin/chat/beschikbaarheid/vrije-dagen', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token(), 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ closed_at: date, reason: reason || null }),
                    });
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) throw new Error((data.errors && Object.values(data.errors)[0][0]) || data.message || 'Toevoegen mislukt.');
                    toast(data.message || 'Toegevoegd.', true);
                    setTimeout(() => window.location.reload(), 800);
                } catch (err) { toast(err.message, false); }
            });

            document.querySelectorAll('[data-closed-del]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!confirm('Deze vrije dag verwijderen?')) return;
                    try {
                        const res = await fetch('/admin/chat/beschikbaarheid/vrije-dagen/' + btn.dataset.closedDel, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token(), 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        if (!res.ok) throw new Error('Verwijderen mislukt.');
                        document.querySelector('[data-closed-row="' + btn.dataset.closedDel + '"]')?.remove();
                        toast('Verwijderd.', true);
                    } catch (e) { toast(e.message, false); }
                });
            });
        })();
    </script>
</x-admin.layout>
