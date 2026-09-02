@if ($paginator->hasPages())
    <nav class="flex items-center gap-1">
        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400"><i data-lucide="chevron-left" class="w-4 h-4"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"><i data-lucide="chevron-left" class="w-4 h-4"></i></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-xs text-slate-400">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-xs font-bold text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-xs hover:bg-slate-50">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50"><i data-lucide="chevron-right" class="w-4 h-4"></i></a>
        @else
            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400"><i data-lucide="chevron-right" class="w-4 h-4"></i></span>
        @endif
    </nav>
@endif
