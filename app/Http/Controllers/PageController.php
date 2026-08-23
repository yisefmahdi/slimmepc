<?php

namespace App\Http\Controllers;

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

