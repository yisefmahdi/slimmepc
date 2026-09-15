<!-- Floating Contact Buttons -->
<div class="fixed bottom-6 right-6 z-[999] flex flex-col items-end gap-3">

    <!-- AI Chatbot -->
    <button type="button" id="openAiChat" aria-label="AI Chat" aria-expanded="false" class="
            group relative flex h-[60px] w-[60px]
            items-center justify-center
            rounded-full
            bg-brand-gradient-br
            text-white
            shadow-[0_12px_35px_rgba(37,99,235,.35)]
            transition-all duration-300
            hover:-translate-y-1
            hover:scale-105
        ">
        <!-- Tooltip -->
        <span class="
                pointer-events-none absolute right-[74px]
                whitespace-nowrap rounded-xl
                bg-slate-950 px-4 py-2
                text-xs font-bold text-white
                opacity-0 shadow-lg
                transition-all duration-200
                group-hover:opacity-100
            ">
            {{ $c['floating']['chat_tooltip'] ?? 'Chat met Slimme-PC' }}
        </span>

        <!-- Chat bars icoon (3 afgeronde strepen) -->
        <svg id="aiChatFabBars" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-7 w-7" aria-hidden="true">
            <rect x="2.5" y="4.5" width="19" height="3.6" rx="1.8" fill="currentColor" />
            <rect x="6" y="10.2" width="12" height="3.6" rx="1.8" fill="currentColor" />
            <rect x="6" y="15.9" width="12" height="3.6" rx="1.8" fill="currentColor" />
        </svg>

        <!-- Sluit icoon (alleen als chat open is) -->
        <svg id="aiChatFabClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.4" stroke="currentColor" class="hidden h-7 w-7" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>

        <!-- Online -->
        <span class="
                absolute bottom-[2px] right-[2px]
                h-[14px] w-[14px]
                rounded-full
                border-[3px] border-white
                bg-brand-accent
            "></span>
    </button>

</div>

