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
     * Oude shop-category-slug (lowercase) → nieuwe route/URL.
     * Alleen 'gaming-pc' bestaat 1-op-1; de rest is best-match.
     */
    private const CATEGORY_MAP = [
        'laptops' => 'labtop',
        'gaming-pc' => 'gaming-pc',
        'pc-laptop-onderdelen' => 'onderdelen',
        'dataopslag' => 'onderdelen',
        'desktops' => 'labtop',
        'refurbished-laptop' => 'labtop',
        'apple' => 'labtop',
        'software' => 'diensten/software-windows',
        'printers' => '/',
        'monitoren' => '/',
        'beveiligingscameras' => '/',
    ];

    public function category(string $slug): RedirectResponse
    {
        $target = self::CATEGORY_MAP[mb_strtolower($slug)] ?? null;

        if ($target === null || $target === '/') {
            return redirect('/', 301);
        }

        // 'diensten/...' is al een compleet pad; kale slugs zijn webshop-categorieën.
        $url = str_starts_with($target, 'diensten/') ? "/{$target}" : "/webshop/{$target}";

        if (! str_starts_with($target, 'diensten/')) {
            $exists = Category::where('slug', $target)->where('status', true)->exists();
            if (! $exists) {
                return redirect('/', 301);
            }
        }

        return redirect($url, 301);
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
