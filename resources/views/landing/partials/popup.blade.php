@php
    $popupCfg = $c['popup'] ?? [];
    $popupEnabled = filter_var($popupCfg['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $popupDelay = max(0, (int) ($popupCfg['delay_seconds'] ?? 2));
    $popupItem = null;

    if ($popupEnabled && !empty($popupCfg['items']) && is_array($popupCfg['items'])) {
        foreach ((array) $popupCfg['items'] as $pi) {
            $pi = is_array($pi) ? $pi : [];
            if (!filter_var($pi['active'] ?? false, FILTER_VALIDATE_BOOLEAN)) continue;
            if (trim((string) ($pi['title'] ?? '')) === '' && trim((string) ($pi['message'] ?? '')) === '') continue;
            $popupItem = $pi;
            break;
        }
    }

    $popupPositions = ['right-bottom', 'left-bottom', 'center', 'bottom-center'];
    $popupPosition = in_array($popupItem['position'] ?? '', $popupPositions, true) ? $popupItem['position'] : 'right-bottom';

    $popupStyles = [
        'info' => ['badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300', 'icon' => 'bg-blue-50 text-blue-600', 'bar' => 'from-blue-500 to-blue-600'],
        'success' => ['badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'icon' => 'bg-emerald-50 text-emerald-600', 'bar' => 'from-emerald-500 to-emerald-600'],
        'warning' => ['badge' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300', 'icon' => 'bg-amber-50 text-amber-600', 'bar' => 'from-amber-500 to-orange-500'],
        'promo' => ['badge' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300', 'icon' => 'bg-violet-50 text-violet-600', 'bar' => 'from-violet-500 to-purple-600'],
    ];
    $popupStyle = $popupStyles[$popupItem['style'] ?? ''] ?? $popupStyles['info'];

    $popupImage = trim((string) ($popupItem['image'] ?? ''));
    $popupImageUrl = $popupImage !== '' ? asset(str_starts_with($popupImage, 'assets/') ? $popupImage : 'assets/img/landing/' . ltrim($popupImage, '/')) : '';

    $popupSig = $popupItem ? md5(($popupItem['title'] ?? '') . '|' . ($popupItem['message'] ?? '') . '|' . ($popupItem['button_url'] ?? '')) : '';
@endphp

@if ($popupItem)
@php
    $isCenter = $popupPosition === 'center';
    $wrapperClass = match ($popupPosition) {
        'left-bottom' => 'fixed bottom-6 left-4 sm:left-6 z-[1000]',
        'center' => 'fixed inset-0 z-[1000] flex items-center justify-center p-4',
        'bottom-center' => 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[1000] w-[520px] max-w-[calc(100vw-2rem)]',
        default => 'fixed bottom-24 right-4 sm:right-6 z-[1000] w-[330px] max-w-[calc(100vw-2rem)]',
    };
    $animClass = match ($popupPosition) {
        'left-bottom' => 'site-popup-anim-left',
        'center' => 'site-popup-anim-center',
        'bottom-center' => 'site-popup-anim-up',
        default => 'site-popup-anim-right',
    };
@endphp
<div id="sitePopup" class="{{ $wrapperClass }} hidden" role="dialog" aria-live="polite" aria-label="{{ $popupItem['title'] ?? 'Melding' }}"
     data-delay="{{ $popupDelay }}" data-sig="{{ $popupSig }}">
    @if ($isCenter)
    <div data-popup-backdrop class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px]"></div>
    @endif
    <div class="{{ $animClass }} relative overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-[0_25px_70px_-15px_rgba(15,23,42,.35)] dark:border-slate-700 dark:bg-slate-900 {{ $isCenter ? 'w-[420px] max-w-full' : '' }}">
        <span class="block h-1 w-full bg-gradient-to-r {{ $popupStyle['bar'] }}"></span>
        <button type="button" data-popup-close aria-label="Sluiten"
                class="absolute right-2.5 top-3.5 flex h-7 w-7 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800">
            <i data-lucide="x" class="h-4 w-4"></i>
        </button>
        <div class="p-4 sm:p-5">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $popupStyle['icon'] }}">
                    <i data-lucide="{{ $popupItem['icon'] ?: 'megaphone' }}" class="h-5 w-5"></i>
                </span>
                <div class="min-w-0">
                    @if (trim((string) ($popupItem['type'] ?? '')) !== '')
                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wide {{ $popupStyle['badge'] }}">{{ $popupItem['type'] }}</span>
                    @endif
                    <h4 class="mt-1 truncate text-[15px] font-extrabold" style="color: var(--c-heading)">{{ $popupItem['title'] }}</h4>
                </div>
            </div>
            @if (trim((string) ($popupItem['message'] ?? '')) !== '')
            <p class="mt-2.5 text-sm leading-relaxed" style="color: var(--c-body)">{{ $popupItem['message'] }}</p>
            @endif
            @if ($popupImageUrl !== '')
            <img src="{{ $popupImageUrl }}" alt="{{ $popupItem['title'] ?? 'Melding' }}" loading="lazy" decoding="async" class="mt-3 max-h-60 w-full rounded-xl bg-slate-100 object-contain dark:bg-slate-800">
            @endif
            @if (trim((string) ($popupItem['button_text'] ?? '')) !== '')
            <a href="{{ $popupItem['button_url'] ?: '#' }}"
               class="bg-brand-gradient-btn mt-3.5 flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90">
                {{ $popupItem['button_text'] }}
                <i data-lucide="arrow-right" class="h-4 w-4"></i>
            </a>
            @endif
        </div>
    </div>
</div>
<script>
(function () {
    var KEY = 'slimmepc-popup-seen';
    var HOUR = 3600 * 1000;
    var el = document.getElementById('sitePopup');
    if (!el) return;

    function read() {
        try { return JSON.parse(localStorage.getItem(KEY) || 'null'); }
        catch (e) { return null; }
    }

    // TIJDELIJK UITGESCHAKELD: maximaal één keer per uur tonen.
    // De popup wordt nu bij elk bezoek getoond. Verwijder de comment hieronder
    // om de uur-limiet weer in te schakelen.
    // var seen = read();
    // if (seen && seen.sig === el.getAttribute('data-sig') && (Date.now() - (seen.t || 0)) < HOUR) return;

    var delay = Math.max(0, parseInt(el.getAttribute('data-delay') || '2', 10)) * 1000;
    var shown = false;

    function markSeen() {
        try { localStorage.setItem(KEY, JSON.stringify({ t: Date.now(), sig: el.getAttribute('data-sig') })); }
        catch (e) {}
    }

    function show() {
        if (shown) return;
        shown = true;
        el.classList.remove('hidden');
        if (window.lucide && lucide.createIcons) lucide.createIcons();
        // markSeen(); // Uitgeschakeld zolang de popup bij elk bezoek getoond wordt.
    }

    function hide() { el.style.display = 'none'; }

    el.querySelector('[data-popup-close]').addEventListener('click', hide);
    var backdrop = el.querySelector('[data-popup-backdrop]');
    if (backdrop) backdrop.addEventListener('click', hide);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hide();
    });

    window.addEventListener('load', function () { setTimeout(show, delay); });
    setTimeout(function () { if (document.readyState === 'complete') show(); }, delay + 1500);
})();
</script>
@endif
