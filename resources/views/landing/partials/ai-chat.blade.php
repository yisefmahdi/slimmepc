<!-- AI Chat Panel (design only — static demo reply, no backend yet) -->
<div id="aiChatPanel"
     class="fixed inset-0 z-[1000] hidden flex-col overflow-hidden border border-slate-200/70 bg-white transition-all duration-300 sm:inset-auto sm:bottom-24 sm:right-6 sm:w-[380px] sm:max-w-[calc(100vw-2rem)] sm:rounded-3xl sm:shadow-[0_24px_70px_rgba(15,23,42,.22)]"
     role="dialog" aria-label="Slimme-PC Assistent" aria-hidden="true">

    <!-- Header -->
    <div class="bg-brand-gradient-br flex items-center gap-3 px-4 py-3.5 text-white">
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20">
            <i data-lucide="sparkles" class="h-5 w-5"></i>
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-extrabold leading-tight">Slimme-PC Assistent</p>
            <p class="flex items-center gap-1.5 text-[11px] font-semibold text-white/85">
                <span class="inline-block h-2 w-2 rounded-full bg-brand-accent"></span>
                Online — reageert direct
            </p>
        </div>
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
        <div id="aiChatSuggestions" class="flex flex-wrap gap-2 pl-1">
            <button type="button" data-suggestion="Ik heb een laptop reparatie nodig" class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100">Laptop reparatie</button>
            <button type="button" data-suggestion="Wat zijn jullie prijzen?" class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100">Prijzen</button>
            <button type="button" data-suggestion="Hoe kan ik contact met jullie opnemen?" class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100">Contact</button>
        </div>
    </div>

    <!-- Typing indicator -->
    <div id="aiChatTyping" class="hidden items-center gap-1.5 bg-[#f7faff] px-5 pb-2">
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:0ms"></span>
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:150ms"></span>
        <span class="h-2 w-2 animate-bounce rounded-full bg-slate-400" style="animation-delay:300ms"></span>
    </div>

    <!-- Input -->
    <form id="aiChatForm" class="flex items-center gap-2 border-t border-slate-200/70 bg-white px-3 py-3" autocomplete="off">
        <input type="text" id="aiChatInput" maxlength="500" placeholder="Typ je vraag..."
               class="h-11 min-w-0 flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-4 focus:ring-blue-100">
        <button type="submit" id="aiChatSend" aria-label="Versturen"
                class="bg-brand-gradient-br flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white shadow-[0_10px_25px_rgba(37,99,235,.30)] transition hover:-translate-y-0.5">
            <i data-lucide="send" class="h-5 w-5"></i>
        </button>
    </form>
</div>
