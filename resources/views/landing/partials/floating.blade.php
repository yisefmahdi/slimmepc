<!-- Floating Contact Buttons -->
<div class="fixed bottom-6 right-6 z-[999] flex flex-col items-end gap-3">

    <!-- AI Chat teaser-bericht (naast de knop) -->
    <div id="aiChatTeaser" class="absolute bottom-1.5 right-[72px] hidden" role="status">
        <div class="site-chat-teaser relative w-[152px] rounded-2xl rounded-br-sm border border-slate-200/80 bg-white px-2.5 py-2 text-center shadow-[0_16px_40px_rgba(15,23,42,.22)] sm:w-auto sm:max-w-none sm:whitespace-nowrap sm:px-3.5 sm:py-2.5 sm:text-left dark:border-slate-700 dark:bg-slate-900">
            <button type="button" data-teaser-close aria-label="Bericht sluiten"
                    class="absolute -right-2 -top-2 flex h-5 w-5 items-center justify-center rounded-full border border-slate-200 bg-white text-xs leading-none text-slate-400 shadow transition hover:text-slate-600 dark:border-slate-700 dark:bg-slate-800">
                ×
            </button>
            <p class="text-[11px] font-semibold leading-tight sm:text-sm sm:leading-normal" style="color: var(--c-heading)">
                {{ $c['floating']['chat_teaser'] ?? 'Stel hier uw vraag' }}
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
    // Wacht tot het volledige document is geparsed: #aiChatPanel staat
    // verderop in de pagina (ai-chat partial komt na floating).
    function init() {
        var teaser = document.getElementById('aiChatTeaser');
        var panel = document.getElementById('aiChatPanel');
        if (!teaser || !panel) return;

    // De teaser nodigt uit om de chat te openen: zichtbaar als het
    // chatpaneel gesloten is EN de chat bemand is (openingstijden).
    // Bij gesloten chat (avond/weekend/feestdag) verschijnt hij nooit.
    var dismissed = false;
    var timer = null;
    var availCache = null;
    var availAt = 0;

    function isOpen() { return !panel.classList.contains('hidden'); }
    function show() {
        if (dismissed || isOpen()) return;
        isAvailable().then(function (ok) {
            if (ok && !dismissed && !isOpen()) {
                teaser.style.display = '';
                teaser.classList.remove('hidden');
            }
        });
    }
    function hide() { teaser.style.display = 'none'; }
    function sync() {
        if (isOpen()) {
            if (timer) { clearTimeout(timer); timer = null; }
            hide();
        } else if (!dismissed && !timer) {
            timer = setTimeout(function () { timer = null; show(); }, 2500);
        }
    }

    // Vraag /ai-chat/status (60s cache) of de chat nu bemand is.
    function isAvailable() {
        var now = Date.now();
        if (availCache !== null && now - availAt < 60000) {
            return Promise.resolve(availCache);
        }
        return fetch('/ai-chat/status', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (res) {
            return res.json().catch(function () { return {}; });
        }).then(function (data) {
            availCache = (data && data.open === true);
            availAt = Date.now();
            return availCache;
        }).catch(function () {
            // Bij twijfel (offline/error): toon de teaser gewoon.
            return true;
        });
    }

    // × verbergt de teaser voor deze paginaweergave.
    teaser.querySelector('[data-teaser-close]').addEventListener('click', function (e) {
        e.stopPropagation();
        dismissed = true;
        if (timer) { clearTimeout(timer); timer = null; }
        hide();
    });

        if (window.MutationObserver) {
            new MutationObserver(sync).observe(panel, { attributes: true, attributeFilter: ['class'] });
        }

        sync();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

