@php $trust = $o['trust'] ?? []; @endphp

@if (count($trust['items'] ?? []))
<section class="bg-white">
    <div class="mx-auto grid max-w-7xl gap-5 px-5 py-7 sm:grid-cols-2 sm:px-8 lg:grid-cols-4 lg:px-10">
        @foreach ($trust['items'] as $item)
        <div class="reveal flex items-center gap-3">
            <i data-lucide="{{ $item['icon'] ?? 'check' }}" class="h-6 w-6 shrink-0 text-blue-600"></i>
            <div>
                <p class="text-sm font-black">{{ $item['title'] ?? '' }}</p>
                @if (!empty($item['subtitle']))
                <p class="text-xs text-slate-500">{{ $item['subtitle'] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif