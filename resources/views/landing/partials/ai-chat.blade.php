<!-- AI Chat Panel (Live Chat: welkom → start → AI/medewerker + offline + rating) -->
<style>
    #aiChatPanel .ai-link { overflow-wrap: anywhere; word-break: break-all; }
    #aiChatMessages { overflow-wrap: anywhere; }
</style>
<div id="aiChatPanel"
     class="fixed inset-0 z-[1000] hidden flex-col overflow-hidden border border-slate-200/70 bg-white transition-all duration-300 sm:inset-auto sm:bottom-24 sm:right-6 sm:h-[600px] sm:max-h-[calc(100dvh-9rem)] sm:w-[380px] sm:max-w-[calc(100vw-2rem)] sm:rounded-3xl sm:shadow-[0_24px_70px_rgba(15,23,42,.22)]"
     role="dialog" aria-label="Slimme-PC Assistent" aria-hidden="true">

    <!-- Header -->
    <div class="bg-brand-gradient-br flex items-center gap-3 px-4 py-3.5 text-white">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20">
            <i data-lucide="sparkles" class="h-5 w-5"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-extrabold leading-tight">Slimme-PC Assistent</p>
            <p id="aiChatPresence" class="flex items-center gap-1.5 text-[11px] font-semibold text-white/85">
                <span class="inline-block h-2 w-2 rounded-full bg-brand-accent"></span>
                <span id="aiChatPresenceText">Online — reageert direct</span>
            </p>
        </div>
        {{-- <button type="button" id="aiChatHistoryBtn" aria-label="Eerdere chats" title="Eerdere chats"
                class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/90 transition hover:bg-white/20">
            <i data-lucide="history" class="h-5 w-5"></i>
        </button> --}}
        <button type="button" id="aiChatSoundBtn" aria-label="Geluid aan/uit" title="Geluid aan/uit"
                class="hidden h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/90 transition hover:bg-white/20">
            <span id="aiChatSoundOn"><i data-lucide="volume-2" class="h-5 w-5"></i></span>
            <span id="aiChatSoundOff" class="hidden"><i data-lucide="volume-x" class="h-5 w-5"></i></span>
        </button>
        <button type="button" id="aiChatHeaderEndBtn" aria-label="Gesprek beëindigen" title="Gesprek beëindigen"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/90 transition hover:bg-white/20">
            <i data-lucide="phone-off" class="h-5 w-5"></i>
        </button>
        <button type="button" id="closeAiChat" aria-label="Chat sluiten"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white/90 transition hover:bg-white/20">
            <i data-lucide="x" class="h-5 w-5"></i>
        </button>
    </div>

    <!-- Messages -->
    <div id="aiChatMessages" class="flex min-h-0 flex-1 flex-col gap-2.5 overflow-y-auto bg-[#f7faff] px-4 py-4 sm:max-h-[540px] sm:min-h-[380px]">
        <div class="flex justify-start">
            <div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">
                Hoi! Ik ben de <strong>Slimme-PC assistent</strong>. Waar kan ik je mee helpen?
            </div>
        </div>

        <!-- Start gate: naam + e-mail (eerst welkom, dan pas starten) -->
        <div id="aiChatGate" class="rounded-2xl bg-white p-3.5 shadow-sm ring-1 ring-slate-200/70">
            <p class="mb-2.5 text-xs font-bold text-slate-600">Vul je gegevens in om te starten:</p>
            <div id="aiChatGateFields" class="space-y-2">
                <input type="text" id="aiChatName" maxlength="255" placeholder="Je naam *"
                       class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
                <input type="email" id="aiChatEmail" maxlength="255" placeholder="Je e-mailadres *"
                       class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
            </div>
            <button type="button" id="aiChatStartBtn"
                    class="bg-brand-gradient-br mt-2.5 flex h-10 w-full items-center justify-center rounded-xl text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5">
                Start chatten
            </button>
            <p id="aiChatGateError" class="hidden mt-2 text-xs font-semibold text-red-600"></p>
        </div>

    </div>

    <!-- History overlay -->
    <div id="aiChatHistory" class="hidden absolute inset-0 z-10 flex-col bg-white">
        <div class="flex items-center justify-between border-b border-slate-200/70 px-4 py-3">
            <p class="text-sm font-extrabold text-slate-800">Eerdere chats</p>
            <button type="button" id="aiChatHistoryClose" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100" aria-label="Sluiten">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
        <div id="aiChatHistoryList" class="flex-1 space-y-2 overflow-y-auto p-4"></div>
    </div>

    <!-- Typing indicator -->
    <div id="aiChatTyping" class="hidden items-center gap-1.5 bg-[#f7faff] px-5 pb-2">
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:0ms"></span>
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:150ms"></span>
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:300ms"></span>
    </div>

    <!-- Action bar: alleen zichtbaar als de AI een medewerker aanbiedt -->
    <div id="aiChatActions" class="hidden items-center gap-2 border-t border-slate-200/70 bg-white px-3 pt-2.5">
        <button type="button" id="aiChatHandoverBtn" class="hidden h-9 flex-1 items-center justify-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-700 transition hover:bg-blue-100">
            <i data-lucide="user-round" class="h-4 w-4"></i> Medewerker spreken
        </button>
    </div>

    <!-- Input -->
    <div id="aiChatPhotoPreview" class="hidden items-center gap-2 border-t border-slate-200/70 bg-white px-3 pt-2.5">
        <img id="aiChatPhotoImg" src="" alt="Foto" class="h-14 w-14 rounded-xl border border-slate-200 object-cover">
        <span id="aiChatPhotoName" class="min-w-0 flex-1 truncate text-xs text-slate-500"></span>
        <button type="button" id="aiChatPhotoRemove" class="flex h-7 w-7 items-center justify-center rounded-lg text-red-500 hover:bg-red-50" aria-label="Foto verwijderen">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
    </div>
    <form id="aiChatForm" class="hidden items-end gap-2 bg-white px-3 py-3" autocomplete="off">
        <input type="file" id="aiChatPhoto" accept=".jpg,.jpeg,.png,.webp,.avif" class="hidden">
        <button type="button" id="aiChatAttachBtn" aria-label="Foto toevoegen" title="Foto toevoegen"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
            <i data-lucide="paperclip" class="h-5 w-5"></i>
        </button>
        <textarea id="aiChatInput" rows="1" maxlength="2000" placeholder="Typ je vraag..."
               class="min-h-[44px] max-h-[120px] min-w-0 flex-1 resize-none overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100"></textarea>
        <button type="submit" id="aiChatSend" aria-label="Versturen"
                class="bg-brand-gradient-br flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white shadow-[0_10px_25px_rgba(37,99,235,.30)] transition hover:-translate-y-0.5">
            <i data-lucide="send" class="h-5 w-5"></i>
        </button>
    </form>

    <!-- Offline view -->
    <div id="aiChatOffline" class="hidden flex-1 flex-col gap-2.5 overflow-y-auto bg-[#f7faff] px-4 py-4">
        <div class="flex justify-start">
            <div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">
                <strong>We zijn nu gesloten.</strong><br>
                <span id="aiChatOfflineReason">Laat je bericht achter — we reageren per e-mail zodra we open zijn.</span>
            </div>
        </div>
        <div id="aiChatOffForm" class="rounded-2xl bg-white p-3.5 shadow-sm ring-1 ring-slate-200/70">
            <div class="space-y-2">
                <input type="text" id="aiChatOffName" maxlength="255" placeholder="Je naam *"
                       class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none placeholder:text-slate-400 focus:border-blue-400 focus:bg-white">
                <input type="email" id="aiChatOffEmail" maxlength="255" placeholder="Je e-mailadres *"
                       class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none placeholder:text-slate-400 focus:border-blue-400 focus:bg-white">
                <textarea id="aiChatOffMsg" rows="3" maxlength="2000" placeholder="Je bericht *"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm outline-none placeholder:text-slate-400 focus:border-blue-400 focus:bg-white"></textarea>
            </div>
            <button type="button" id="aiChatOffSend"
                    class="bg-brand-gradient-br mt-2.5 flex h-10 w-full items-center justify-center gap-2 rounded-xl text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 disabled:opacity-60">
                <svg id="aiChatOffSpinner" class="hidden h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span id="aiChatOffSendLabel">Verstuur bericht</span>
            </button>
            <p id="aiChatOffError" class="hidden mt-2 text-xs font-semibold text-red-600"></p>
        </div>
        <div id="aiChatOffDone" class="hidden flex-col gap-2.5">
            <div class="flex justify-start">
                <div class="max-w-[85%] rounded-2xl rounded-tl-md bg-white px-3.5 py-2.5 text-[13px] leading-relaxed text-slate-700 shadow-sm ring-1 ring-slate-200/70">
                    <p id="aiChatOffThanks" dir="auto"><strong>Bedankt!</strong><br>We hebben je bericht ontvangen en reageren per e-mail.</p>
                </div>
            </div>
            <button type="button" id="aiChatOffAgain"
                    class="flex h-10 w-full items-center justify-center rounded-xl border border-blue-200 bg-blue-50 text-sm font-bold text-blue-700 transition hover:bg-blue-100">
                Nog een bericht sturen
            </button>
        </div>
    </div>

    <!-- Rating view -->
    <div id="aiChatRating" class="hidden flex-1 flex-col items-center justify-center gap-3 bg-[#f7faff] px-6 py-8 text-center">
        <div id="aiChatRateForm" class="flex flex-col items-center justify-center gap-3">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 text-blue-600">
                <i data-lucide="star" class="h-7 w-7"></i>
            </span>
            <p class="text-sm font-extrabold text-slate-800">Hoe vond je dit gesprek?</p>
            <div id="aiChatStars" class="flex items-center gap-1.5">
                <button type="button" data-stars="1" aria-label="1 ster" class="ai-star p-1 text-slate-300 transition hover:scale-110"><i data-lucide="star" class="h-8 w-8"></i></button>
                <button type="button" data-stars="2" aria-label="2 sterren" class="ai-star p-1 text-slate-300 transition hover:scale-110"><i data-lucide="star" class="h-8 w-8"></i></button>
                <button type="button" data-stars="3" aria-label="3 sterren" class="ai-star p-1 text-slate-300 transition hover:scale-110"><i data-lucide="star" class="h-8 w-8"></i></button>
                <button type="button" data-stars="4" aria-label="4 sterren" class="ai-star p-1 text-slate-300 transition hover:scale-110"><i data-lucide="star" class="h-8 w-8"></i></button>
                <button type="button" data-stars="5" aria-label="5 sterren" class="ai-star p-1 text-slate-300 transition hover:scale-110"><i data-lucide="star" class="h-8 w-8"></i></button>
            </div>
            <textarea id="aiChatRateComment" rows="2" maxlength="1000" placeholder="Opmerking (optioneel)"
                      class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none placeholder:text-slate-400 focus:border-blue-400"></textarea>
            <div class="flex w-full gap-2">
                <button type="button" id="aiChatRateSkip" class="flex h-10 flex-1 items-center justify-center rounded-xl border border-slate-200 text-sm font-bold text-slate-500">Overslaan</button>
                <button type="button" id="aiChatRateSend" class="bg-brand-gradient-br flex h-10 flex-1 items-center justify-center rounded-xl text-sm font-bold text-white">Versturen</button>
            </div>
        </div>
        <div id="aiChatRatedDone" class="hidden flex-col items-center justify-center gap-3">
            <span class="flex h-14 w-14 items-center justify-center rounded-full bg-green-50 text-green-600">
                <i data-lucide="check" class="h-7 w-7"></i>
            </span>
            <p class="text-sm font-extrabold text-slate-800">Bedankt voor je beoordeling!</p>
            <p id="aiChatRatedStars" class="text-xl font-bold text-amber-400"></p>
            <button type="button" id="aiChatNewChatBtn"
                    class="bg-brand-gradient-br mt-1 flex h-10 w-full items-center justify-center rounded-xl text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5">
                Nieuw gesprek starten
            </button>
        </div>
    </div>

    <!-- Foto lightbox (popup binnen het paneel) -->
    <div id="aiChatLightbox" class="absolute inset-0 z-20 hidden flex-col items-center justify-center bg-slate-950/85 p-4 backdrop-blur-sm" role="dialog" aria-label="Foto vergroten" aria-hidden="true">
        <div class="relative rounded-2xl bg-white p-2 shadow-2xl" style="max-width:min(320px,86vw);">
            <button type="button" id="aiChatLightboxClose" aria-label="Sluiten"
                    class="absolute flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg transition hover:bg-slate-700" style="top:-14px;right:-14px;">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
            <img id="aiChatLightboxImg" src="" alt="Foto vergroot" class="w-auto max-w-full rounded-xl object-contain" style="max-height:60vh;">
        </div>
    </div>
</div>

<script>
    window.AiChat = Object.assign({}, window.AiChat, {
        user: @json(['name' => auth()->user()?->name, 'email' => auth()->user()?->email]),
        routes: {
            status: '{{ route('ai-chat.status') }}',
            start: '{{ route('ai-chat.start') }}',
            messages: '{{ route('ai-chat.messages') }}',
            send: '{{ route('ai-chat.send') }}',
            handover: '{{ route('ai-chat.handover') }}',
            offline: '{{ route('ai-chat.offline') }}',
            close: '{{ route('ai-chat.close') }}',
            rate: '{{ route('ai-chat.rate') }}',
            history: '{{ route('ai-chat.history') }}'
        }
    });
</script>
