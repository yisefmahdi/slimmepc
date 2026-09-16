<?php

namespace App\Services\Chat;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Zoekt beschikbare webshop-producten voor de chat.
 * Geeft alleen publieke velden + echte links terug (nooit verzinnen).
 */
class ChatProductSearch
{
    /**
     * Fallback-keten: tokens → enkelvoud → categorie → budget.
     * "Geen laptops" mag pas als ALLES leeg is.
     *
     * @return array{budget: int|null, products: array<int, array<string, mixed>>}
     */
    public function search(string $userText, int $limit = 3, ?int $budgetOverride = null, ?string $categorySlug = null): array
    {
        // Budget en categorie komen van de agent (tool-args).
        // Geen bedrag-extractie uit tekst meer: dit is pure retrieval.
        $budget = $budgetOverride;
        $categoryId = $this->resolveCategoryId($categorySlug);

        $words = $this->words($userText);

        $candidates = $this->matchTokens($words, $categoryId);

        // Stap 2: enkelvoud proberen (laptops → laptop). Generieke
        // morfologie, geen woordenlijsten.
        if ($candidates->isEmpty()) {
            $singular = array_values(array_unique(array_filter(
                array_map(fn ($w) => $this->singularize($w), $words)
            )));
            if ($singular !== $words) {
                $candidates = $this->matchTokens($singular, $categoryId);
                $words = $singular;
            }
        }

        // Stap 3: hele categorie (alles op voorraad) als tokens niets vonden.
        if ($candidates->isEmpty() && $categoryId) {
            $candidates = Product::where('status', true)->with('category')
                ->where('category_id', $categoryId)
                ->orderBy('id')->limit(40)->get();
        }

        // Stap 4: budget-zoekers krijgen ALTIJD het prijsbereik erbij.
        if ($budget) {
            $inRange = Product::where('status', true)->with('category')->orderBy('id')->limit(80)->get()
                ->filter(function (Product $p) use ($budget, $categoryId) {
                    $v = (float) $p->discounted_price;
                    if ($v < $budget * 0.5 || $v > $budget * 1.5) {
                        return false;
                    }

                    return ! $categoryId || (int) $p->category_id === $categoryId;
                });
            $candidates = $candidates->concat($inRange)->unique('id')->values();
        }

        // Geen woordmatch maar wél budget: puur op prijs zoeken.
        if ($candidates->isEmpty() && $budget) {
            $q = Product::where('status', true)->with('category')->orderBy('id')->limit(60);
            if ($categoryId) {
                $q->where('category_id', $categoryId);
            }
            $candidates = $q->get();
        }

        if ($candidates->isEmpty() && ! $budget) {
            return ['budget' => null, 'products' => []];
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
            // Expliciet gevraagde categorie = relevantie op zich: leden
            // overleven het score-filter, de agent beoordeelt de fit.
            if ($categoryId && (int) $p->category_id === $categoryId) {
                $score += 2;
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

        // Alles leeg maar enkelvoud verschilt? Hele keten opnieuw met
        // enkelvoud (idempotent — geen oneindige recursie mogelijk).
        if (! $items && ! $budget) {
            $singular = array_values(array_unique(array_filter(
                array_map(fn ($w) => $this->singularize($w), $this->words($userText))
            )));
            if ($singular !== $this->words($userText)) {
                return $this->search(implode(' ', $singular), $limit, null, $categorySlug);
            }
        }

        return ['budget' => $budget, 'products' => $items];
    }

    /**
     * Token-match op catalogus + categorie in de haystack.
     * Met categorie-slug als harde filter (type-validatie).
     *
     * @param  array<int, string>  $words
     * @return Collection<int, Product>
     */
    protected function matchTokens(array $words, ?int $categoryId)
    {
        $query = Product::where('status', true)->with('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($words) {
            $query->where(function ($q) use ($words) {
                foreach ($words as $w) {
                    $lower = mb_strtolower($w);
                    $q->orWhere('title', 'like', "%{$w}%")
                        ->orWhere('brand', 'like', "%{$w}%")
                        ->orWhere('sku', 'like', "%{$w}%")
                        ->orWhere('description', 'like', "%{$w}%")
                        ->orWhereRaw('LOWER(features) LIKE ?', ['%'.$lower.'%'])
                        ->orWhereHas('category', fn ($cq) => $cq->where('name', 'like', "%{$w}%")->orWhere('slug', 'like', "%{$w}%"));
                }
            });

            return $query->orderBy('id')->limit(40)->get();
        }

        return collect();
    }

    protected function resolveCategoryId(?string $slug): ?int
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return null;
        }

        return Category::where('status', true)->where('slug', $slug)->value('id');
    }

    /**
     * Generiek enkelvoud (meervoud-s eraf). Morfologie, geen lijst.
     */
    protected function singularize(string $word): string
    {
        if (mb_strlen($word) > 4 && str_ends_with($word, 's') && ! str_ends_with($word, 'ss')) {
            return mb_substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Bouwt kaart-payloads voor product-ids (alleen actieve producten).
     *
     * @param  array<int, int>  $ids
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
            'category' => $p->category?->name,
            'category_slug' => $p->category?->slug,
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

    /**
     * Pure tokenizer voor catalogus-retrieval: splitsen op witruimte,
     * leestekens eraf, verder niets. Geen stopwoorden, geen lijsten —
     * de agent formuleert de query, dit knipt hem alleen in stukjes.
     *
     * @return array<int, string>
     */
    protected function words(string $text): array
    {
        $out = [];
        foreach (preg_split('/\s+/u', mb_strtolower($text)) ?? [] as $w) {
            $w = trim($w, " \t\n\r\0\x0B.,!?;:\"'()€");
            if ($w !== '' && ! is_numeric($w)) {
                $out[] = $w;
            }
        }

        return array_values(array_unique(array_slice($out, 0, 8)));
    }
}
