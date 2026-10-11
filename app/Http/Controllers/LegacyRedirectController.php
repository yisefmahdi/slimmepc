<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;

/**
 * 301-redirects van het OUDE slimme-pc.nl (vorige Laravel-app) naar de
 * nieuwe structuur — SEO-behoud bij domeinmigratie (zie project-structure §45).
 *
 * - Statische redirects staan als Route::redirect(..., 301) in routes/web.php.
 * - Dynamische oude URLs (/shop/{slug}, /product/{slug}) landen hier:
 *   categorie → nieuw slug-equivalent (of home), product → canonieke
 *   /webshop/{category}/{product} als hij bestaat, anders /zoeken?q=...
 */
class LegacyRedirectController extends Controller
{
    /**
     * Oude shop-category-slug (lowercase) → nieuwe slug. Bestaat de slug
     * 1-op-1 (bv. laptops, gaming-pc) dan wint die altijd (zelfde URL,
     * alleen /shop → /webshop prefix) — pas daarna deze hernoem-map.
     */
    private const CATEGORY_RENAMES = [
        'refurbished-laptop' => 'refurbished',
        'pc-laptop-onderdelen' => 'computeronderdelen',
        'labtop' => 'laptops',
        'onderdelen' => 'computeronderdelen',
    ];

    public function category(string $slug): RedirectResponse
    {
        $slug = mb_strtolower($slug);

        // 1. Zelfde slug nog actief? Dan alleen prefix wisselen (beste SEO).
        if (Category::where('slug', $slug)->where('status', true)->exists()) {
            return redirect("/webshop/{$slug}", 301);
        }

        // 2. Bekende hernoeming meenemen.
        $target = self::CATEGORY_RENAMES[$slug] ?? null;
        if ($target !== null && Category::where('slug', $target)->where('status', true)->exists()) {
            return redirect("/webshop/{$target}", 301);
        }

        // 3. Software-categorie verdwenen? Dan de software-dienstpagina.
        if ($slug === 'software') {
            return redirect('/diensten/software-windows', 301);
        }

        return redirect('/', 301);
    }

    public function product(string $slug): RedirectResponse
    {
        $product = Product::where('slug', $slug)->where('status', true)->first();

        if ($product && $product->category && $product->category->status) {
            return redirect()->route('webshop.product', [$product->category->slug, $product->slug], 301);
        }

        // Geen (actief) product: stuur door naar zoeken met leesbare termen
        // i.p.v. een kale homepage (behoudt relevantie voor Google + bezoeker).
        $terms = trim(str_replace(['-', '_'], ' ', $slug));

        return redirect()->route('zoeken', $terms !== '' ? ['q' => $terms] : [], 301);
    }
}
