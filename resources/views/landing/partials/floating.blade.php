<!-- Floating Contact Buttons -->
<div class="fixed bottom-6 right-6 z-[999] flex flex-col items-end gap-3">

    <!-- AI Chat teaser-bericht (naast de knop) -->
    <div id="aiChatTeaser" class="absolute bottom-1.5 right-[72px] hidden" role="status">
        <div class="site-chat-teaser relative max-w-[210px] rounded-2xl rounded-br-sm border border-slate-200/80 bg-white px-3.5 py-2.5 shadow-[0_16px_40px_rgba(15,23,42,.22)] sm:max-w-none sm:whitespace-nowrap dark:border-slate-700 dark:bg-slate-900">
            <button type="button" data-teaser-close aria-label="Bericht sluiten"
                    class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full border border-slate-200 bg-white text-xs leading-none text-slate-400 shadow transition hover:text-slate-600 dark:border-slate-700 dark:bg-slate-800">
                ×
            </button>
            <p class="flex items-center gap-2 text-[13px] font-semibold sm:text-sm" style="color: var(--c-heading)">
                <span data-teaser-dots class="flex shrink-0 items-center gap-1" aria-hidden="true">
                    <span class="site-chat-dot"></span>
                    <span class="site-chat-dot" style="animation-delay:.15s"></span>
                    <span class="site-chat-dot" style="animation-delay:.3s"></span>
                </span>
                <span data-teaser-text data-full="{{ $c['floating']['chat_teaser'] ?? 'Stel hier uw vraag' }}"></span>
            </p>
        </div>
    </div>

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

<script>
(function () {
    var teaser = document.getElementById('aiChatTeaser');
    var chatBtn = document.getElementById('openAiChat');
    var panel = document.getElementById('aiChatPanel');
    if (!teaser || !chatBtn) return;

    // Geen permanente opslag: de teaser verschijnt bij elke paginabezoek opnieuw.
    // × verbergt hem alleen voor deze paginaweergave.
    var started = false;

    function hide() { teaser.style.display = 'none'; }
    function show() {
        teaser.style.display = '';
        teaser.classList.remove('hidden');
        typeText();
    }

    // Typ het bericht letter voor letter; verberg de stippen als het klaar is.
    function typeText() {
        if (started) return;
        started = true;
        var out = teaser.querySelector('[data-teaser-text]');
        var dots = teaser.querySelector('[data-teaser-dots]');
        if (!out) return;
        var full = out.getAttribute('data-full') || '';
        var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduce || !full) {
            out.textContent = full;
            if (dots) dots.style.display = 'none';
            return;
        }
        var i = 0;
        var timer = setInterval(function () {
            i++;
            out.textContent = full.slice(0, i);
            if (i >= full.length) {
                clearInterval(timer);
                if (dots) dots.style.display = 'none';
            }
        }, 45);
    }

    // Toon het bericht kort na het laden (typ-effect via de stippen).
    window.addEventListener('load', function () {
        setTimeout(show, 2500);
    });
    setTimeout(function () {
        if (document.readyState === 'complete') show();
    }, 4000);

    teaser.querySelector('[data-teaser-close]').addEventListener('click', function (e) {
        e.stopPropagation();
        hide();
    });

    // Verberg de teaser zodra de chat geopend wordt.
    chatBtn.addEventListener('click', hide);

    // Extra zekering: verberg zodra het chatpaneel zichtbaar wordt
    // (onafhankelijk van de klik-volgorde met ai-chat.js).
    if (panel && window.MutationObserver) {
        new MutationObserver(function () {
            if (!panel.classList.contains('hidden')) hide();
        }).observe(panel, { attributes: true, attributeFilter: ['class'] });
    }
})();
</script>

