{{-- Payment method badges (cart + checkout), CMS-driven via productinfo `payment_badges`.
     Each row: optional logo image + label text. Without uploaded logos the
     original styled text badges render (zero visual change pre-upload).
     Props: $payBadges (array), $variant ('cart'|'checkout'). --}}
@php
    $payBadges = $payBadges ?? [];
    $variant = $variant ?? 'cart';
    $badgeHasImages = collect($payBadges)->contains(fn ($b) => !empty($b['image'] ?? null));
    $badgeCols = count($payBadges) <= 1 ? 'grid-cols-1' : (count($payBadges) === 2 ? 'grid-cols-2' : 'grid-cols-3');
    $badgeImg = function ($v) {
        if (!$v) return null;
        if (str_starts_with($v, 'http')) return $v;
        if (str_starts_with($v, 'assets/')) return asset($v);
        return asset('assets/img/landing/' . ltrim($v, '/'));
    };
@endphp
@if(!$badgeHasImages)
    @if($variant === 'checkout')
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="h-11 rounded-lg border flex items-center justify-center text-[11px] font-bold text-pink-700">iDEAL</div>
            <div class="h-11 rounded-lg border flex items-center justify-center text-[9px] font-bold text-blue-700">Bancontact</div>
            <div class="h-11 rounded-lg border flex items-center justify-center font-bold text-blue-700 text-xs">VISA</div>
        </div>
    @else
        <div class="grid grid-cols-3 gap-2">
            <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[10px] font-black text-[#D50067]">iDEAL</span></div>
            <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[8px] font-black text-[#163A77]">Bancontact</span></div>
            <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center"><span class="text-[12px] font-black italic text-[#17357A]">VISA</span></div>
        </div>
    @endif
@else
    @if($variant === 'checkout')
        <div class="grid {{ $badgeCols }} gap-2 text-center">
            @foreach($payBadges as $pb)
                <div class="h-11 rounded-lg border flex items-center justify-center px-2">
                    @if(!empty($pb['image']))<img src="{{ $badgeImg($pb['image']) }}" alt="{{ $pb['label'] ?? 'Betaalmethode' }}" class="max-h-[24px] max-w-full object-contain" loading="lazy">@else<span class="text-[11px] font-bold text-slate-700">{{ $pb['label'] ?? '' }}</span>@endif
                </div>
            @endforeach
        </div>
    @else
        <div class="grid {{ $badgeCols }} gap-2">
            @foreach($payBadges as $pb)
                <div class="h-[40px] rounded-[5px] border border-[#DCE4EF] flex items-center justify-center px-2">
                    @if(!empty($pb['image']))<img src="{{ $badgeImg($pb['image']) }}" alt="{{ $pb['label'] ?? 'Betaalmethode' }}" class="max-h-[24px] max-w-full object-contain" loading="lazy">@else<span class="text-[10px] font-black text-slate-700">{{ $pb['label'] ?? '' }}</span>@endif
                </div>
            @endforeach
        </div>
    @endif
@endif
