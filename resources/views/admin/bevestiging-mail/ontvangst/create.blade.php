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
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between">
                        <label class="mb-1.5 flex items-center gap-2 text-sm font-semibold" style="color: var(--c-heading)">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4 text-slate-500"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
                            Foto's van het apparaat
                        </label>
                        <span id="ontvangstPhotoCounter" class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-400">0 / 20</span>
                    </div>
                    <input type="file" id="ontvangstPhotos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">
                    <input type="file" id="ontvangstPhotosCamera" accept="image/*" capture="environment" class="hidden">
                    <div id="ontvangstDropzone"
                         class="group relative flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-blue-200/80 bg-blue-50/30 p-5 text-center transition hover:border-blue-500 hover:bg-blue-50/70 dark:border-blue-900/40 dark:bg-slate-900/20 dark:hover:border-blue-500">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm transition group-hover:scale-105 group-hover:bg-blue-600 group-hover:text-white dark:bg-slate-800 dark:text-blue-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                Sleep foto's hierheen of <span class="text-blue-600 hover:underline">klik om te selecteren</span>
                            </p>
                            <p class="mt-0.5 text-xs text-slate-400">Optioneel · Kies uit bestanden of maak direct een foto met de camera · Maximaal 20 foto's (PNG, JPG, WEBP, AVIF · 10MB per foto)</p>
                        </div>
                    </div>
                    <div id="ontvangstPhotosPreview" class="mt-3 flex flex-wrap items-center gap-2.5 sm:gap-3"></div>
                    <p id="ontvangstPhotosFeedback" class="hidden mt-1 text-xs font-semibold text-amber-600 dark:text-amber-400"></p>
                    <p class="field-error hidden mt-1 text-xs font-semibold text-red-600" data-error-for="photos"></p>
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

    {{-- ============ Foto-bron kiezen (bestanden of camera) ============ --}}
    <x-admin.modal id="ontvangstPhotoSource" title="Foto toevoegen" subtitle="Kies uit je bestanden of maak direct een foto." size="sm">
        {{-- Stap 1: bron kiezen --}}
        <div id="ontvangstSourceChoice" class="grid grid-cols-2 gap-3">
            <button type="button" id="ontvangstPickFiles"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-blue-300 bg-blue-50/40 px-4 py-6 text-blue-700 transition hover:border-blue-500 hover:bg-blue-50/80 dark:bg-slate-900/40 dark:text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-9 w-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776" />
                </svg>
                <span class="text-sm font-bold">Bestanden</span>
                <span class="text-[11px] font-medium opacity-70">Kies één of meer foto's</span>
            </button>
            <button type="button" id="ontvangstPickCamera"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-emerald-300 bg-emerald-50/40 px-4 py-6 text-emerald-700 transition hover:border-emerald-500 hover:bg-emerald-50/80 dark:bg-slate-900/40 dark:text-emerald-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor" class="h-9 w-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                </svg>
                <span class="text-sm font-bold">Camera</span>
                <span class="text-[11px] font-medium opacity-70">Maak direct een foto</span>
            </button>
        </div>

        {{-- Stap 2: live camera (werkt met webcam én aangesloten USB-camera) --}}
        <div id="ontvangstCameraView" class="hidden flex-col gap-3">
            <div class="relative overflow-hidden rounded-2xl bg-slate-950">
                <video id="ontvangstCameraVideo" autoplay playsinline muted
                       class="aspect-[4/3] w-full object-cover"></video>
                <span class="absolute left-2 top-2 flex items-center gap-1.5 rounded-full bg-black/60 px-2.5 py-1 text-[11px] font-bold text-white">
                    <span class="inline-block h-2 w-2 animate-pulse rounded-full bg-red-500"></span>
                    LIVE
                </span>
            </div>
            <label class="text-xs font-semibold" style="color: var(--c-heading)">
                Camera kiezen
                <select id="ontvangstCameraDevice"
                        class="form-input mt-1 h-10 w-full text-xs"></select>
            </label>
            <p id="ontvangstCameraError" class="hidden rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300"></p>
            <div class="flex gap-2">
                <button type="button" id="ontvangstCaptureBtn"
                        class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-sm font-bold text-white shadow-[0_10px_25px_rgba(5,150,105,.25)] transition hover:bg-emerald-700 disabled:opacity-60">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z" />
                    </svg>
                    Foto maken
                </button>
                <button type="button" id="ontvangstCameraBackBtn"
                        class="inline-flex h-11 items-center justify-center rounded-xl border px-4 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                        style="color: var(--c-heading); border-color: var(--c-input-border)">
                    Terug
                </button>
            </div>
            <canvas id="ontvangstCameraCanvas" class="hidden"></canvas>
        </div>

        <x-slot name="footer">
            <button type="button" data-modal-close
                    class="inline-flex h-11 items-center justify-center rounded-xl border px-5 text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-slate-800"
                    style="color: var(--c-heading); border-color: var(--c-input-border)">
                Annuleren
            </button>
        </x-slot>
    </x-admin.modal>

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

            // ---- Foto's: dashboard-stijl preview (dropzone + tiles + DataTransfer) ----
            const photoInput = document.getElementById('ontvangstPhotos');
            const cameraInput = document.getElementById('ontvangstPhotosCamera');
            const pickFilesBtn = document.getElementById('ontvangstPickFiles');
            const pickCameraBtn = document.getElementById('ontvangstPickCamera');
            const SOURCE_MODAL = 'ontvangstPhotoSource';

            function openSourcePicker(){
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.open(SOURCE_MODAL); return; }
                const m = document.getElementById('modal-' + SOURCE_MODAL);
                if(m) m.classList.remove('hidden');
                else if(photoInput) photoInput.click();
            }
            function closeSourcePicker(){
                if(window.SlimmePC && window.SlimmePC.modal){ window.SlimmePC.modal.close(SOURCE_MODAL); return; }
                const m = document.getElementById('modal-' + SOURCE_MODAL);
                if(m) m.classList.add('hidden');
            }

            if(pickFilesBtn && photoInput){
                pickFilesBtn.addEventListener('click', () => { photoInput.click(); closeSourcePicker(); });
            }

            // ---- Live camera (webcam + aangesloten USB-camera + mobiele camera) ----
            const choiceView = document.getElementById('ontvangstSourceChoice');
            const cameraView = document.getElementById('ontvangstCameraView');
            const video = document.getElementById('ontvangstCameraVideo');
            const deviceSelect = document.getElementById('ontvangstCameraDevice');
            const camError = document.getElementById('ontvangstCameraError');
            const captureBtn = document.getElementById('ontvangstCaptureBtn');
            const camBackBtn = document.getElementById('ontvangstCameraBackBtn');
            const canvas = document.getElementById('ontvangstCameraCanvas');
            const sourceModalEl = document.getElementById('modal-' + SOURCE_MODAL);
            let camStream = null;

            function showCamError(text){
                if(!camError) return;
                if(!text){ camError.textContent = ''; camError.classList.add('hidden'); return; }
                camError.textContent = text;
                camError.classList.remove('hidden');
            }
            function stopCamera(){
                if(camStream){ camStream.getTracks().forEach(t => { try{ t.stop(); }catch(e){} }); camStream = null; }
                if(video) video.srcObject = null;
            }
            function showChoice(){
                stopCamera();
                if(cameraView){ cameraView.classList.add('hidden'); cameraView.classList.remove('flex'); }
                if(choiceView){ choiceView.classList.remove('hidden'); }
            }
            async function listCameras(){
                if(!deviceSelect) return;
                try{
                    const devs = await navigator.mediaDevices.enumerateDevices();
                    const cams = devs.filter(d => d.kind === 'videoinput');
                    deviceSelect.innerHTML = '';
                    if(!cams.length){ deviceSelect.innerHTML = '<option value="">Geen camera gevonden</option>'; return; }
                    cams.forEach((c, i) => {
                        const o = document.createElement('option');
                        o.value = c.deviceId;
                        o.textContent = c.label || ('Camera ' + (i + 1));
                        deviceSelect.appendChild(o);
                    });
                }catch(e){ /* labels blijven leeg, opname werkt alsnog */ }
            }
            async function startCamera(deviceId){
                stopCamera();
                showCamError('');
                const base = { width: { ideal: 1920 }, height: { ideal: 1080 } };
                const wanted = deviceId ? Object.assign({ deviceId: { exact: deviceId } }, base) : Object.assign({ facingMode: 'environment' }, base);
                try{
                    camStream = await navigator.mediaDevices.getUserMedia({ audio: false, video: wanted });
                }catch(err){
                    if(deviceId){
                        try{ camStream = await navigator.mediaDevices.getUserMedia({ audio: false, video: base }); }
                        catch(e2){ showCamError('Camera niet beschikbaar. Controleer de cameratoestemming in de browser en probeer opnieuw.'); return; }
                    }else{
                        showCamError('Camera niet beschikbaar. Controleer de cameratoestemming in de browser en probeer opnieuw.');
                        return;
                    }
                }
                if(video){
                    video.srcObject = camStream;
                    try{ await video.play(); }catch(e){}
                }
            }
            async function openCameraView(){
                if(!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia){
                    closeSourcePicker();
                    if(cameraInput) cameraInput.click();
                    return;
                }
                if(choiceView) choiceView.classList.add('hidden');
                if(cameraView){ cameraView.classList.remove('hidden'); cameraView.classList.add('flex'); }
                if(captureBtn) captureBtn.disabled = true;
                try{
                    const tmp = await navigator.mediaDevices.getUserMedia({ audio: false, video: true });
                    tmp.getTracks().forEach(t => { try{ t.stop(); }catch(e){} });
                }catch(e){
                    showCamError('Geen toegang tot de camera. Sta cameragebruik toe in de browser en probeer opnieuw.');
                    if(captureBtn) captureBtn.disabled = false;
                    return;
                }
                await listCameras();
                await startCamera(deviceSelect && deviceSelect.value ? deviceSelect.value : null);
                if(captureBtn) captureBtn.disabled = false;
            }

            if(pickCameraBtn){
                pickCameraBtn.addEventListener('click', () => { openCameraView(); });
            }
            if(deviceSelect){
                deviceSelect.addEventListener('change', function(){ startCamera(this.value || null); });
            }
            if(camBackBtn){
                camBackBtn.addEventListener('click', () => { showChoice(); });
            }
            if(captureBtn && canvas){
                captureBtn.addEventListener('click', () => {
                    if(!camStream || !video || !video.videoWidth){
                        showCamError('Camera is nog aan het opstarten — wacht een moment en probeer opnieuw.');
                        return;
                    }
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    canvas.toBlob((blob) => {
                        if(!blob){ showCamError('Foto maken mislukt, probeer opnieuw.'); return; }
                        const file = new File([blob], 'camera-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                        addFiles([file]);
                        closeSourcePicker();
                        showChoice();
                    }, 'image/jpeg', 0.92);
                });
            }
            // Camera altijd stoppen bij sluiten van de popup (overlay / Annuleren / Escape).
            if(sourceModalEl){
                sourceModalEl.addEventListener('click', (e) => {
                    if(e.target.closest('[data-modal-close],[data-modal-overlay]')) showChoice();
                });
            }
            document.addEventListener('keydown', (e) => { if(e.key === 'Escape') showChoice(); });
            if(cameraInput){
                cameraInput.addEventListener('change', function(){ addFiles(this.files); this.value = ''; });
            }
            const dropzone = document.getElementById('ontvangstDropzone');
            const previewGrid = document.getElementById('ontvangstPhotosPreview');
            const counter = document.getElementById('ontvangstPhotoCounter');
            const feedback = document.getElementById('ontvangstPhotosFeedback');
            const MAX_PHOTOS = 20;
            let stagedFiles = [];

            function showFeedback(text){
                if(!feedback) return;
                if(!text){ feedback.textContent=''; feedback.classList.add('hidden'); return; }
                feedback.textContent = text;
                feedback.classList.remove('hidden');
            }

            function syncInput(){
                const dt = new DataTransfer();
                stagedFiles.forEach(f => dt.items.add(f));
                photoInput.files = dt.files;
                if(counter) counter.textContent = stagedFiles.length + ' / ' + MAX_PHOTOS;
                if(dropzone) dropzone.classList.toggle('hidden', stagedFiles.length > 0);
            }

            function renderPreview(){
                if(!previewGrid) return;
                previewGrid.innerHTML = '';
                stagedFiles.forEach((file, idx) => {
                    const url = URL.createObjectURL(file);
                    const card = document.createElement('div');
                    card.className = 'group relative h-20 w-20 sm:h-24 sm:w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm transition hover:shadow-md dark:border-slate-800 dark:bg-slate-900';
                    card.innerHTML = '<img src="'+url+'" alt="Foto '+(idx+1)+'" class="h-full w-full object-cover">'
                        + '<button type="button" data-idx="'+idx+'" aria-label="Verwijderen" class="absolute right-1 top-1 flex h-6 w-6 items-center justify-center rounded-lg bg-black/60 text-white opacity-0 transition group-hover:opacity-100 hover:bg-red-600">'
                        + '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg></button>'
                        + '<span class="absolute bottom-1 left-1 rounded-md bg-black/60 px-1.5 py-0.5 text-[10px] font-bold text-white">'+(idx+1)+'</span>';
                    previewGrid.appendChild(card);
                });
                const addTile = document.createElement('button');
                addTile.type = 'button';
                addTile.title = 'Foto toevoegen';
                addTile.className = 'flex h-20 w-20 sm:h-24 sm:w-24 shrink-0 cursor-pointer flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-blue-300 bg-blue-50/40 text-blue-600 transition hover:border-blue-500 hover:bg-blue-50/80 shadow-sm';
                addTile.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg><span class="text-[10px] font-bold">Toevoegen</span>';
                addTile.addEventListener('click', () => openSourcePicker());
                previewGrid.appendChild(addTile);
                previewGrid.querySelectorAll('button[data-idx]').forEach(b => {
                    b.addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        stagedFiles.splice(parseInt(b.dataset.idx, 10), 1);
                        syncInput(); renderPreview(); showFeedback('');
                    });
                });
            }

            function addFiles(files){
                const incoming = Array.from(files || []).filter(f => f.type.startsWith('image/'));
                if(!incoming.length) return;
                if(stagedFiles.length + incoming.length > MAX_PHOTOS){
                    showFeedback('Maximaal '+MAX_PHOTOS+' foto\'s per ontvangst. Eerste '+MAX_PHOTOS+' worden bewaard.');
                    incoming.splice(MAX_PHOTOS - stagedFiles.length);
                } else {
                    showFeedback('');
                }
                const oversized = incoming.filter(f => f.size > 10*1024*1024);
                if(oversized.length) showFeedback('Elke foto mag maximaal 10MB zijn — te grote bestanden zijn overgeslagen.');
                incoming.filter(f => f.size <= 10*1024*1024).forEach(f => stagedFiles.push(f));
                syncInput(); renderPreview();
            }

            if(dropzone && photoInput){
                dropzone.addEventListener('click', () => openSourcePicker());
                ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('border-blue-500','bg-blue-100/50'); }));
                ['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.remove('border-blue-500','bg-blue-100/50'); }));
                dropzone.addEventListener('drop', (e) => addFiles(e.dataTransfer.files));
                photoInput.addEventListener('change', function(){ addFiles(this.files); this.value = ''; });
                syncInput(); renderPreview();
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

                try{
                    const res = await fetch('{{ route('admin.bevestiging-mail.ontvangst.store') }}', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || document.querySelector('input[name=_token]')?.value || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: fd,
                    });
                    const data = await res.json().catch(()=>({}));
                    if(res.ok && data.receipt){
                        msg.textContent = '✓ ' + (data.message || 'Ontvangst succesvol aangemaakt en verzonden!');
                        msg.className = 'mt-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-bold text-green-700';
                        msg.classList.remove('hidden');
                        form.reset();
                        stagedFiles = []; syncInput(); renderPreview();
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
                            const base = field.split('.')[0];
                            const input = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${base}"]`) || form.querySelector(`[name="${base}[]"]`);
                            const errEl = (input && input.parentElement) ? input.parentElement.querySelector('.field-error') : document.querySelector('[data-error-for="photos"]');
                            if(input) input.classList.add('!border-red-500');
                            if(errEl){ errEl.textContent = msgs[0]; errEl.classList.remove('hidden'); }
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
