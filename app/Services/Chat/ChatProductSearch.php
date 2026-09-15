<?php

namespace App\Services\Chat;

use App\Models\Product;
use Illuminate\Support\Facades\Log;

/**
 * Zoekt beschikbare webshop-producten voor de chat.
 * Geeft alleen publieke velden + echte links terug (nooit verzinnen).
 */
class ChatProductSearch
{
    /**
     * @return array{budget: int|null, products: array<int, array<string, mixed>>}
     */
    public function search(string $userText, int $limit = 3, ?int $budgetOverride = null): array
    {
        $budget = $budgetOverride ?? $this->extractBudget($userText);
        $words = array_merge($this->words($userText), ArabicText::toDutchTerms(ArabicText::tokens($userText)));

        $query = Product::where('status', true)->with('category');

        if ($words) {
            $query->where(function ($q) use ($words) {
                foreach ($words as $w) {
                    $lower = mb_strtolower($w);
                    $q->orWhere('title', 'like', "%{$w}%")
                        ->orWhere('brand', 'like', "%{$w}%")
                        ->orWhere('sku', 'like', "%{$w}%")
                        ->orWhere('description', 'like', "%{$w}%")
                        ->orWhereRaw('LOWER(features) LIKE ?', ['%'.$lower.'%']);
                }
            });
        } elseif (! $budget) {
            return ['budget' => null, 'products' => []];
        }

        $candidates = $query->orderBy('id')->limit(40)->get();

        // Budget-zoekers krijgen ALTIJD het prijsbereik erbij (unie, geen fallback):
        // zo blijft er keuze (meerdere kaarten) in plaats van één vast product.
        if ($budget) {
            $inRange = Product::where('status', true)->with('category')->orderBy('id')->limit(80)->get()
                ->filter(function (Product $p) use ($budget) {
                    $v = (float) $p->discounted_price;

                    return $v >= $budget * 0.5 && $v <= $budget * 1.5;
                });
            $candidates = $candidates->concat($inRange)->unique('id')->values();
        }

        // Geen woordmatch maar wél budget: puur op prijs zoeken.
        if ($candidates->isEmpty() && $budget) {
            $candidates = Product::where('status', true)->with('category')->orderBy('id')->limit(60)->get();
        }

        $scored = [];
        foreach ($candidates as $p) {
            $hay = mb_strtolower($p->title.' '.$p->brand.' '.($p->sku ?? '').' '.strip_tags((string) $p->description));
            $score = 0;
            foreach ($words as $w) {
                if (str_contains($hay, $w)) {
                    $score += mb_strlen($w) >= 4 ? 2 : 1;
                }
            }
            // Voorraad eerst: uitverkocht zakt weg maar blijft zichtbaar als alternatief.
            if (($p->stock_status ?? 'in_stock') !== 'in_stock') {
                $score -= 5;
            }
            $scored[] = ['score' => $score, 'product' => $p];
        }

        $price = fn (Product $p) => (float) $p->discounted_price;

        if ($budget) {
            // Binnen 0.5x–1.5x, gesorteerd op dichtstbijzijnde prijs.
            $scored = array_filter($scored, function ($s) use ($budget, $price) {
                $v = $price($s['product']);

                return $v >= $budget * 0.5 && $v <= $budget * 1.5;
            });
            usort($scored, fn ($a, $b) => abs($price($a['product']) - $budget) <=> abs($price($b['product']) - $budget));
        } else {
            usort($scored, fn ($a, $b) => $b['score'] <=> $a['score'] ?: $a['product']->id <=> $b['product']->id);
            $scored = array_filter($scored, fn ($s) => $s['score'] > 0);
        }

        $items = [];
        foreach (array_slice(array_values($scored), 0, $limit) as $s) {
            $items[] = $this->toCard($s['product']);
        }

        return ['budget' => $budget, 'products' => $items];
    }

    /**
     * Bouwt kaart-payloads voor product-ids (alleen actieve producten).
     *
     * @param array<int, int> $ids
     * @return array<int, array<string, mixed>>
     */
    public function cardsForIds(array $ids): array
    {
        if (! $ids) {
            return [];
        }

        $products = Product::where('status', true)->with('category')
            ->whereIn('id', array_slice(array_values(array_unique(array_map('intval', $ids))), 0, 3))
            ->get()
            ->keyBy('id');

        $cards = [];
        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if (isset($products[$id])) {
                $cards[] = $this->toCard($products[$id]);
            }
            if (count($cards) >= 3) {
                break;
            }
        }

        return $cards;
    }

    /**
     * @return array<string, mixed>
     */
    protected function toCard(Product $p): array
    {
        $categorySlug = $p->category?->slug ?? 'laptops';

        $specs = [];
        foreach (array_slice((array) ($p->features ?? []), 0, 5) as $f) {
            $t = is_array($f) ? trim((string) ($f['title'] ?? '')) : '';
            $v = is_array($f) ? trim((string) ($f['value'] ?? '')) : trim((string) $f);
            if ($t !== '' || $v !== '') {
                $specs[] = trim($t.': '.$v, ': ');
            }
        }

        return [
            'id' => $p->id,
            'title' => $p->title,
            'brand' => $p->brand,
            'price' => (float) $p->discounted_price,
            'old_price' => $p->old_price ? (float) $p->old_price : null,
            'in_stock' => ($p->stock_status ?? 'in_stock') === 'in_stock',
            'delivery' => $p->delivery_time,
            'rating' => $p->rating_avg ? (float) $p->rating_avg : null,
            'rating_count' => (int) ($p->rating_count ?? 0),
            'specs' => implode(' | ', $specs),
            'image' => $this->imageUrl($p),
            'url' => route('webshop.product', [$categorySlug, $p->slug]),
        ];
    }

    protected function imageUrl(Product $p): ?string
    {
        $img = $p->main_image;
        if (! $img) {
            return null;
        }
        if (str_starts_with($img, 'http')) {
            return $img;
        }
        if (str_starts_with($img, 'assets/')) {
            return asset($img);
        }

        return asset('storage/'.ltrim($img, '/'));
    }

    protected function extractBudget(string $text): ?int
    {
        if (preg_match('/(\d{2,5})\s*(€|euro|يورو)/iu', $text, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    protected function words(string $text): array
    {
        $stop = ['een', 'het', 'de', 'van', 'voor', 'met', 'die', 'dat', 'the', 'and', 'rond', 'onder', 'tussen', 'حدود', 'بسعر', 'يورو', 'مثلا', 'مثل', 'في', 'على', 'الى', 'إلى', 'من', 'مع', 'هل', 'يوجد', 'عندكم', 'بدي', 'ابغى', 'اريد', 'أريد'];
        $out = [];
        foreach (preg_split('/\s+/u', mb_strtolower($text)) ?? [] as $w) {
            $w = trim($w, " \t\n\r\0\x0B.,!?;:\"'()€");
            if (mb_strlen($w) >= 3 && ! in_array($w, $stop, true) && ! is_numeric($w)) {
                $out[] = $w;
            }
        }

        return array_values(array_unique(array_slice($out, 0, 8)));
    }
}
