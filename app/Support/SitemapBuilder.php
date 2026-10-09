<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

/**
 * Centrale bron voor alle publiek indexeerbare URL's.
 *
 * Wordt gebruikt door:
 * - SitemapController (/sitemap.xml, dynamisch)
 * - GenerateSitemap command (php artisan sitemap:generate -> public/sitemap.xml)
 *
 * Privepagina's (cart, checkout, payment/*, technician/*, account, wishlist...)
 * worden hier bewust NIET opgenomen.
 */
class SitemapBuilder
{
    /**
     * @return array<int, array{loc: string, lastmod: string|null, changefreq: string, priority: string}>
     */
    public static function urls(): array
    {
        $urls = [];

        $add = function (string $routeName, array $params = [], string $changefreq = 'weekly', string $priority = '0.8', ?string $lastmod = null) use (&$urls) {
            if (! Route::has($routeName)) {
                return;
            }

            try {
                $urls[] = [
                    'loc' => route($routeName, $params),
                    'lastmod' => $lastmod ?? now()->toDateString(),
                    'changefreq' => $changefreq,
                    'priority' => $priority,
                ];
            } catch (\Throwable) {
                // Route kon niet gegenereerd worden (bv. ontbrekende param) — sla over.
            }
        };

        // Home — hoogste prioriteit
        $add('home', [], 'daily', '1.0');

        // Statische landingspagina's
        $add('tarieven', [], 'monthly', '0.8');
        $add('contact', [], 'monthly', '0.7');
        $add('over-ons', [], 'monthly', '0.7');
        $add('privacy', [], 'yearly', '0.3');
        $add('voorwaarden', [], 'yearly', '0.3');
        $add('reparatie', [], 'weekly', '0.9');
        $add('afspraak', [], 'weekly', '0.9');
        $add('lidmaatschap.show', [], 'weekly', '0.8');

        // Diensten (slugs uit config/cms.php -> /diensten/{slug})
        $serviceSlugs = array_keys((array) config('cms.service_slugs', []));
        foreach ($serviceSlugs as $slug) {
            $add('service.show', ['slug' => $slug], 'weekly', '0.8');
        }

        // Webshop categorieen (alleen actief)
        $categories = Category::where('status', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['slug', 'updated_at']);

        foreach ($categories as $category) {
            if (empty($category->slug)) {
                continue;
            }
            $add(
                'webshop.category',
                ['slug' => $category->slug],
                'weekly',
                '0.8',
                $category->updated_at?->toDateString() ?? now()->toDateString()
            );
        }

        // Webshop producten (alleen actief + met actieve categorie)
        // Chunken zodat grote catalogi geen geheugenprobleem geven.
        Product::where('status', true)
            ->with('category:id,slug,status')
            ->orderBy('updated_at', 'desc')
            ->chunk(500, function ($products) use (&$add) {
                foreach ($products as $product) {
                    if (empty($product->slug) || ! $product->category || empty($product->category->slug)) {
                        continue;
                    }
                    if (! $product->category->status) {
                        continue;
                    }
                    $add(
                        'webshop.product',
                        [$product->category->slug, $product->slug],
                        'weekly',
                        '0.9',
                        $product->updated_at?->toDateString() ?? now()->toDateString()
                    );
                }
            });

        return $urls;
    }

    public static function xml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= '  <url>'."\n";
            $xml .= '    <loc>'.e($url['loc']).'</loc>'."\n";
            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod']).'</lastmod>'."\n";
            }
            $xml .= '    <changefreq>'.e($url['changefreq']).'</changefreq>'."\n";
            $xml .= '    <priority>'.e($url['priority']).'</priority>'."\n";
            $xml .= '  </url>'."\n";
        }

        $xml .= '</urlset>'."\n";

        return $xml;
    }
}
