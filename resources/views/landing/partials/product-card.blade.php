{{-- Shared webshop product card (webshop grid + wishlist). Expects: $product, $favoriteIds (array, optional). --}}
@php
    $favoriteIds = $favoriteIds ?? [];
    $hasDiscount = ($product->old_price && (float)$product->old_price > (float)$product->price) || ($product->discount_value && $product->discounted_price < (float)$product->price);
    $finalPrice = $hasDiscount && $product->discount_value ? $product->discounted_price : (float)$product->price;
    $oldPrice = (float)($product->old_price ?: $product->price);

    // Badge logic matching products.html
    $badge = null;
    $badgeClass = 'bg-blue-500';
    if ($hasDiscount) {
        $badge = 'Aanbieding';
        $badgeClass = 'bg-red-500';
    } elseif ($product->is_featured) {
        $badge = 'Populair';
        $badgeClass = 'bg-emerald-500';
    } elseif (str_contains(strtolower($product->title), 'probook') || str_contains(strtolower($product->title), '15s')) {
        $badge = 'Bestseller';
        $badgeClass = 'bg-green-500';
    } elseif (str_contains(strtolower($product->title), 'vivobook')) {
        $badge = 'Nieuw';
        $badgeClass = 'bg-blue-500';
    }

    // Specs formatting - supports both legacy string[] and new [{title,value}]
    $specs = '';
    if (!empty($product->features) && is_array($product->features)) {
        $featStrs = array_map(function($f){
            if(is_array($f) && isset($f['value'])){ $t=trim($f['title']??''); $v=trim($f['value']); return $t!=='' ? $t.': '.$v : $v; }
            return (string)$f;
        }, $product->features);
        $featStrs = array_values(array_filter($featStrs));
        if(!empty($featStrs)) $specs = implode(' · ', $featStrs);
    }
    if ($specs === '' && !empty($product->summary)) {
        $specs = $product->summary;
    } elseif ($product->brand) {
        $specs = $product->brand . ($product->category ? ' · ' . $product->category->name : '');
    } else {
        $specs = '';
    }

    // Image helper - use local placeholder if no image
    $galleryFirst = !empty($product->gallery_images) && is_array($product->gallery_images) ? ($product->gallery_images[0] ?? null) : null;
    $pImg = $product->main_image ?: $galleryFirst;
    $placeholderSrc = asset('assets/img/product-placeholder.jpg');
    if ($pImg) {
        if (str_starts_with($pImg, 'http')) {
            $imgSrc = $pImg;
        } elseif (str_starts_with($pImg, 'assets/')) {
            $imgSrc = asset($pImg);
        } else {
            $imgSrc = asset('storage/' . $pImg);
        }
    } else {
        $imgSrc = $placeholderSrc;
    }

    // Rating — dynamisch uit DB (avg + count), alleen goedgekeurde reviews
    $reviewCount = (int) ($product->rating_count ?? 0);
    $avgRating = (float) ($product->rating_avg ?? 0);

    $isFav = in_array($product->id, $favoriteIds, true);
@endphp

<article
    class="product-card reveal flex flex-col rounded-2xl border border-slate-200 bg-white p-4"
    data-price="{{ $finalPrice }}"
    data-brand="{{ strtolower($product->brand ?? '') }}"
    data-title="{{ strtolower($product->title) }}"
    data-product-id="{{ $product->id }}"
>
    @if($badge)
        <div class="flex items-start justify-between min-h-[28px]">
            <span class="rounded-full {{ $badgeClass }} px-2.5 py-1 text-[9px] font-bold text-white">
                {{ $badge }}
            </span>

            <button class="heart-btn {{ $isFav ? 'active' : '' }}" style="{{ $isFav ? 'color: #e11d48;' : '' }}" onclick="toggleWishlist({{ $product->id }}, this)" type="button" aria-label="Favoriet">
                <i data-lucide="heart" class="w-[18px] h-[18px]" {{ $isFav ? 'fill="currentColor"' : '' }}></i>
            </button>
        </div>
    @else
        <div class="flex justify-end min-h-[28px]">
            <button class="heart-btn {{ $isFav ? 'active' : '' }}" style="{{ $isFav ? 'color: #e11d48;' : '' }}" onclick="toggleWishlist({{ $product->id }}, this)" type="button" aria-label="Favoriet">
                <i data-lucide="heart" class="w-[18px] h-[18px]" {{ $isFav ? 'fill="currentColor"' : '' }}></i>
            </button>
        </div>
    @endif

    <a href="{{ route('webshop.product', [$product->category->slug, $product->slug]) }}" class="product-media flex h-[180px] items-center justify-center">
        <img
            src="{{ $imgSrc }}"
            class="product-image max-h-[155px] max-w-full rounded-xl object-contain"
            alt="{{ $product->title }}"
            onerror="this.src='{{ $placeholderSrc }}'"
        >
    </a>

    <div class="product-body">
        <a href="{{ route('webshop.product', [$product->category->slug, $product->slug]) }}">
            <h3 class="text-[12px] font-extrabold leading-5 hover:text-blue-600 transition">
                {{ $product->title }}
            </h3>
        </a>

        <p class="mt-1 text-[10px] text-slate-500">
            {{ $specs }}
        </p>
    </div>

    <div class="product-footer mt-auto pt-4">
        @if($hasDiscount)
            <div class="flex items-center gap-2">
                <span class="text-[16px] font-black">
                    €{{ number_format($finalPrice, 2, ',', '.') }}
                </span>
                <span class="text-[10px] text-slate-400 line-through">
                    €{{ number_format($oldPrice, 2, ',', '.') }}
                </span>
            </div>
        @else
            <div class="text-[16px] font-black">
                €{{ number_format($finalPrice, 2, ',', '.') }}
            </div>
        @endif

        <div class="mt-3 flex items-center justify-between">
            <div class="text-[11px] flex items-center gap-1.5">
                <span class="text-amber-400">
                    @php $r = $avgRating; @endphp
                    @for($i=1;$i<=5;$i++)
                        @if($r >= $i) ★ @elseif($r >= $i-0.5) ⯪ @else <span class="text-slate-300">★</span> @endif
                    @endfor
                </span>
                <span class="font-bold {{ $reviewCount>0 ? 'text-slate-700' : 'text-slate-400' }}">{{ $reviewCount>0 ? number_format($avgRating,1) : '—' }}</span>
                <span class="text-slate-400">({{ $reviewCount }})</span>
            </div>

            <x-add-to-cart :product="$product" variant="grid" />
        </div>
    </div>
</article>
