<?php

namespace App\Http\Controllers;

use App\Support\SitemapBuilder;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Dynamische sitemap voor Google (https://www.sitemaps.org/).
     * Gecachet voor 24 uur — legen via `php artisan sitemap:generate` of cache:clear.
     */
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addDay(), function () {
            return SitemapBuilder::xml(SitemapBuilder::urls());
        });

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
