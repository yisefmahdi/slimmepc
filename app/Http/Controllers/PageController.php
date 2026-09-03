<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function home()
    {
        $version = Cms::version();
        $cacheKey = "cms.page.html.home.{$version}";

        /*
         * Logged-in users get a personalized header (name + logout dropdown),
         * so never serve them the shared cached HTML.
         */
        if (!Auth::check()) {
            $cached = Cache::get($cacheKey);

            if (is_string($cached)) {
                return response($cached)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        $c = Cms::page('home');
        $design = Cms::design();

        $homeProducts = Product::where('status', true)
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(12)
            ->get();

        // If no products are explicitly selected for Home, fallback to latest 4 active products
        if ($homeProducts->isEmpty()) {
            $homeProducts = Product::where('status', true)
                ->with('category')
                ->latest()
                ->take(4)
                ->get();
        }

        if ($homeProducts->isNotEmpty()) {
            $mappedProducts = [];
            foreach ($homeProducts as $fp) {
                $specs = '';
                if (!empty($fp->features) && is_array($fp->features)) {
                    $featStrs = array_map(function ($f) {
                        if (is_array($f) && isset($f['value'])) {
                            $t = trim($f['title'] ?? ''); $v = trim($f['value']);
                            return $t !== '' ? $t . ': ' . $v : $v;
                        }
                        return (string) $f;
                    }, $fp->features);
                    $featStrs = array_values(array_filter($featStrs));
                    if (!empty($featStrs)) $specs = implode(' · ', array_slice($featStrs, 0, 3));
                }
                if ($specs === '' && $fp->brand) {
                    $specs = $fp->brand . ($fp->category ? ' · ' . $fp->category->name : '');
                }

                $badge = '';
                $badgeColor = 'blue';
                if ($fp->discount_value && $fp->discounted_price < $fp->price) {
                    $badge = $fp->discount_type === 'percentage' ? "-{$fp->discount_value}%" : 'Korting';
                    $badgeColor = 'amber';
                } elseif ($fp->is_featured) {
                    $badge = 'Populair';
                    $badgeColor = 'blue';
                }

                if ($fp->main_image) {
                    if (str_starts_with($fp->main_image, 'http')) $imageUrl = $fp->main_image;
                    elseif (str_starts_with($fp->main_image, 'assets/')) $imageUrl = asset($fp->main_image);
                    elseif (str_starts_with($fp->main_image, 'storage/')) $imageUrl = asset($fp->main_image);
                    else $imageUrl = asset('storage/' . $fp->main_image);
                } else {
                    $imageUrl = asset('assets/img/landing/laptop-fallback.png');
                }

                $mappedProducts[] = [
                    'id' => $fp->id,
                    'title' => $fp->title,
                    'specs' => $specs,
                    'price' => '€' . number_format($fp->price, 2),
                    'in_stock' => $fp->stock_status === 'in_stock',
                    'image' => $imageUrl,
                    'is_db_image' => true,
                    'link' => '/webshop',
                    'badge' => $badge,
                    'badge_color' => $badgeColor,
                ];
            }
            $c['shop']['products'] = $mappedProducts;
        }

        $html = view('landing.home', compact('c', 'design'))->render();

        // Only cache for guests — a logged-in response must never pollute the shared cache
        if (!Auth::check()) {
            Cache::put($cacheKey, $html, now()->addMonth());
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function tarieven()
    {
        $version = Cms::version();
        $cacheKey = "cms.page.html.tarieven.{$version}";

        if (!Auth::check()) {
            $cached = Cache::get($cacheKey);

            if (is_string($cached)) {
                return response($cached)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        // Header/footer live on the 'home' page; the tarieven content lives on 'tarieven'.
        $c = Cms::page('home');
        $t = Cms::page('tarieven');
        $design = Cms::design();

        $html = view('landing.tarieven', compact('c', 't', 'design'))->render();

        if (!Auth::check()) {
            Cache::put($cacheKey, $html, now()->addMonth());
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function contact()
    {
        $version = Cms::version();
        $cacheKey = "cms.page.html.contact.{$version}";

        if (!Auth::check()) {
            $cached = Cache::get($cacheKey);

            if (is_string($cached)) {
                return response($cached)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        // Header/footer live on the 'home' page; the contact content lives on 'contact'.
        $c = Cms::page('home');
        $p = Cms::page('contact');
        $design = Cms::design();

        $html = view('landing.contact', compact('c', 'p', 'design'))->render();

        if (!Auth::check()) {
            Cache::put($cacheKey, $html, now()->addMonth());
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function overons()
    {
        $version = Cms::version();
        $cacheKey = "cms.page.html.overons.{$version}";

        if (!Auth::check()) {
            $cached = Cache::get($cacheKey);

            if (is_string($cached)) {
                return response($cached)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        // Header/footer live on the 'home' page; the over-ons content lives on 'overons'.
        $c = Cms::page('home');
        $o = Cms::page('overons');
        $design = Cms::design();

        $html = view('landing.overons', compact('c', 'o', 'design'))->render();

        if (!Auth::check()) {
            Cache::put($cacheKey, $html, now()->addMonth());
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function reparatie()
    {
        // NEVER cache this page: it contains a CSRF-protected form. A cached HTML
        // would serve guests a stale _token and every submit would fail with 419.
        $c = Cms::page('home');
        $s = Cms::page('reparatie');
        $design = Cms::design();

        $html = view('landing.service-reparatie', compact('c', 's', 'design'))->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Afspraak aan huis page.
     */
    public function afspraak()
    {
        $c = Cms::page('home');
        $s = Cms::page('afspraak');

        $html = view('landing.service-afspraak', compact('c', 's'))->render();

        return response($html)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function service(string $slug)
    {
        $map = config('cms.service_slugs', []);
        if (! array_key_exists($slug, $map)) {
            abort(404);
        }

        $pageKey = $map[$slug];
        $version = Cms::version();
        $cacheKey = "cms.page.html.service.{$pageKey}.{$version}";

        if (!Auth::check()) {
            $cached = Cache::get($cacheKey);
            if (is_string($cached)) {
                return response($cached)->header('Content-Type', 'text/html; charset=UTF-8');
            }
        }

        $s = Cms::page($pageKey);
        $design = Cms::design();

        // Placeholder pages that have not been filled in yet return 404.
        if (empty($s['hero']['title1'] ?? '')) {
            abort(404);
        }

        // Header/footer live on the 'home' page.
        $c = Cms::page('home');

        $view = view()->exists('landing.service-' . $pageKey) ? 'landing.service-' . $pageKey : 'landing.service';
        $html = view($view, compact('c', 's', 'design', 'slug', 'pageKey'))->render();

        if (!Auth::check()) {
            Cache::put($cacheKey, $html, now()->addMonth());
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }
}

