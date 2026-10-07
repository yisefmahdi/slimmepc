{{-- Shared webshop trust bar (category page + wishlist + order history + cart).
     Driven by productinfo CMS block `webshop_trust` (icon/title/subtitle rows,
     add & delete in admin). Falls back to the 4 hardcoded cards pre-seed.
     Pass ['bare' => true] to render only the inner card (for pages that
     already provide their own section/container, like the cart page). --}}
@php
    $trustBarBare = $bare ?? false;
    $trustBarItems = isset($pi) ? ($pi['info']['webshop_trust'] ?? []) : [];
    if (empty($trustBarItems)) $trustBarItems = [
        ['icon' => 'truck', 'title' => 'Gratis verzending', 'subtitle' => 'vanaf €75'],
        ['icon' => 'map-pin', 'title' => 'Afhalen in Apeldoorn', 'subtitle' => 'Binnen openingstijden'],
        ['icon' => 'shield-check', 'title' => 'Garantie', 'subtitle' => 'Op onze producten'],
        ['icon' => 'lock-keyhole', 'title' => 'Veilig betalen', 'subtitle' => 'Betrouwbare betaalmethodes'],
    ];
    $trustBarCols = count($trustBarItems) === 1 ? 'lg:grid-cols-1'
        : (count($trustBarItems) === 2 ? 'lg:grid-cols-2'
        : (count($trustBarItems) === 3 ? 'lg:grid-cols-3' : 'lg:grid-cols-4'));
@endphp
@if(!$trustBarBare)
<section class="pb-12">
    <div class="max-w-[1450px] mx-auto px-5 sm:px-7 lg:px-10 xl:px-12">
@endif
        <div class="reveal grid grid-cols-1 sm:grid-cols-2 {{ $trustBarCols }} gap-5 rounded-2xl bg-white border border-slate-200 p-5 {{ $trustBarBare ? 'mt-8 sm:mt-10' : '' }}">
            @foreach($trustBarItems as $tbi)
                <div class="flex items-center gap-3">
                    <div class="flex w-10 h-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i data-lucide="{{ $tbi['icon'] ?? 'badge-check' }}" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <div class="text-[12px] font-bold">{{ $tbi['title'] ?? '' }}</div>
                        @if(!empty($tbi['subtitle']))<div class="text-[10px] text-slate-600 mt-1">{{ $tbi['subtitle'] }}</div>@endif
                    </div>
                </div>
            @endforeach
        </div>
@if(!$trustBarBare)
    </div>
</section>
@endif
