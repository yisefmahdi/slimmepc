@props(['product', 'quantity' => 1, 'variant' => 'grid'])

@php
  $isGrid = $variant === 'grid';
  $isDetails = $variant === 'details';
  $isSticky = $variant === 'sticky';
  $isUpsell = $variant === 'upsell';
@endphp

@if($isGrid)
<button type="button"
    data-cart-add
    data-product-id="{{ $product->id }}"
    data-quantity="{{ $quantity }}"
    class="cart-add-btn flex w-9 h-9 items-center justify-center rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shrink-0"
    aria-label="Toevoegen aan winkelwagen">
    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
</button>
@elseif($isDetails)
<button type="button"
    data-cart-add
    data-product-id="{{ $product->id }}"
    data-quantity-source="qtyInput"
    class="cart-add-main w-full sm:flex-1 h-[58px] rounded-xl bg-gradient-to-r from-[#0647ca] via-[#0757ef] to-[#2877ff] text-white font-semibold shadow-[0_18px_50px_rgba(7,87,239,.18)] flex items-center justify-center gap-3 transition hover:opacity-95 active:scale-[0.98]">
    <i class="fa-solid fa-cart-shopping"></i>
    <span class="cart-text">In winkelwagen</span>
</button>
@elseif($isSticky)
<button type="button"
    data-cart-add
    data-product-id="{{ $product->id }}"
    data-quantity-source="qtyInput"
    class="cart-add-main h-11 flex-1 md:flex-none md:min-w-[260px] px-6 bg-gradient-to-r from-[#0647ca] via-[#0757ef] to-[#2877ff] text-white rounded-lg text-sm font-semibold flex items-center justify-center gap-3 hover:opacity-95 transition">
    <i class="fa-solid fa-cart-shopping"></i> In winkelwagen
</button>
@elseif($isUpsell)
<button type="button"
    data-cart-add
    data-product-id="{{ $product->id }}"
    class="h-[34px] px-4 rounded-[6px] border border-[#0759F5] text-[#0759F5] text-[11px] font-semibold inline-flex items-center gap-2 hover:bg-[#0759F5] hover:text-white transition">
    <i data-lucide="plus" class="w-[15px] h-[15px]"></i>
    <span>Toevoegen</span>
</button>
@else
<button type="button"
    data-cart-add
    data-product-id="{{ $product->id }}"
    data-quantity="{{ $quantity }}"
    class="cart-add-btn inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-blue-700 transition">
    <i data-lucide="shopping-cart" class="w-4 h-4"></i> Toevoegen
</button>
@endif
