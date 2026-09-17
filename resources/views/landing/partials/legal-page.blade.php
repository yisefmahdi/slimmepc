@php
    $inhoud = $l['inhoud'] ?? [];
    $raw = (string) ($inhoud['content'] ?? '');
    // Eenvoudige opmaak: "## kop" wordt een kop, een witregel start een nieuwe alinea.
    $html = '';
    foreach (preg_split('/\R{2,}/', $raw) as $para) {
        $para = trim($para);
        if ($para === '') continue;
        if (str_starts_with($para, '## ')) {
            $html .= '<h2 class="mt-10 first:mt-0 text-xl font-black text-[#0b1f4d] sm:text-2xl">' . e(trim(substr($para, 3))) . '</h2>';
        } else {
            $html .= '<p class="mt-4 leading-relaxed text-slate-700">' . nl2br(e($para)) . '</p>';
        }
    }
@endphp

<section class="relative overflow-hidden bg-gradient-to-br from-[#edf6ff] via-white to-[#f4f9ff]">
    <div class="pointer-events-none absolute -right-24 -top-24 h-[420px] w-[420px] rounded-full bg-blue-300/20 blur-[110px]"></div>
    <div class="pointer-events-none absolute left-[35%] top-[30%] h-[260px] w-[260px] rounded-full bg-cyan-200/20 blur-[90px]"></div>

    <div class="relative mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8 lg:py-20">
        @if (!empty($inhoud['badge']))
        <p class="mb-4 text-sm font-bold uppercase tracking-wide text-blue-600">{{ $inhoud['badge'] }}</p>
        @endif

        <h1 class="text-4xl font-black leading-[1.05] text-[#0b1f4d] sm:text-5xl">
            {{ $inhoud['title_line1'] ?? '' }}
            @if (!empty($inhoud['title_line2']))
            <br><span class="text-blue-600">{{ $inhoud['title_line2'] }}</span>
            @endif
        </h1>

        @if (!empty($inhoud['description']))
        <p class="mt-5 max-w-2xl text-lg leading-relaxed text-slate-700">{{ $inhoud['description'] }}</p>
        @endif

        <article class="mt-10 rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_14px_35px_rgba(15,23,42,0.06)] sm:p-10">
            @if ($html !== '')
            {!! $html !!}
            @else
            <p class="leading-relaxed text-slate-500">Deze pagina wordt binnenkort gevuld.</p>
            @endif

            @if (!empty($inhoud['updated_label']))
            <p class="mt-10 border-t border-slate-100 pt-5 text-xs text-slate-400">{{ $inhoud['updated_label'] }}</p>
            @endif
        </article>
    </div>
</section>
