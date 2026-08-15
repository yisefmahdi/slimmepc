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
}

