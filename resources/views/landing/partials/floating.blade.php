<!-- Floating Contact Buttons -->
<div class="fixed bottom-6 right-6 z-[999] flex flex-col items-end gap-3">

    <!-- AI Chatbot -->
    <a href="{{ $c['floating']['chat_url'] ?? '#' }}" class="
            group relative flex h-[60px] w-[60px]
            items-center justify-center
            rounded-full
            bg-brand-gradient-br
            text-white
            shadow-[0_12px_35px_rgba(37,99,235,.35)]
            transition-all duration-300
            hover:-translate-y-1
            hover:scale-105
        " aria-label="AI Chat">
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

        <i data-lucide="message-circle" class="h-7 w-7"></i>

        <!-- AI sparkle -->
        <span class="
                absolute right-[12px] top-[10px]
                flex h-[17px] w-[17px]
                items-center justify-center
                rounded-full
                bg-white text-brand-primary
                shadow-sm
            ">
            <i data-lucide="sparkles" class="h-[11px] w-[11px]"></i>
        </span>

        <!-- Online -->
        <span class="
                absolute bottom-[2px] right-[2px]
                h-[14px] w-[14px]
                rounded-full
                border-[3px] border-white
                bg-brand-accent
            "></span>
    </a>

    <!-- WhatsApp -->
    <a href="{{ $c['floating']['whatsapp_url'] ?? '#' }}" class="
            group relative flex h-[58px] w-[58px]
            items-center justify-center
            rounded-full
            bg-[#25D366]
            text-white
            shadow-[0_12px_35px_rgba(37,211,102,.30)]
            transition-all duration-300
            hover:-translate-y-1
            hover:scale-105
            hover:shadow-[0_16px_40px_rgba(37,211,102,.40)]
        " aria-label="WhatsApp">
        <!-- Tooltip -->
        <span class="
                pointer-events-none absolute right-[72px]
                whitespace-nowrap rounded-xl
                bg-slate-950 px-4 py-2
                text-xs font-bold text-white
                opacity-0 shadow-lg
                transition-all duration-200
                group-hover:opacity-100
            ">
            {{ $c['floating']['whatsapp_tooltip'] ?? 'Stuur ons een WhatsApp' }}
        </span>

        <i data-lucide="message-circle" class="h-7 w-7"></i>
    </a>
</div>

