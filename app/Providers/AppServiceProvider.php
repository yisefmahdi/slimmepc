<?php

namespace App\Providers;

use App\Models\Category;
use App\Services\InboundContactFetcher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(InboundContactFetcher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share active webshop categories with the landing header dropdown
        View::composer('landing.partials.header', function ($view) {
            $categories = Cache::remember('webshop.header.categories', 3600, function () {
                return Category::where('status', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'icon', 'description', 'image', 'sort_order']);
            });
            $view->with('webshopCategories', $categories);
        });

        // Share the same categories with the footer
        View::composer('landing.partials.footer', function ($view) {
            $categories = Cache::remember('webshop.header.categories', 3600, function () {
                return Category::where('status', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug', 'icon', 'description', 'image', 'sort_order']);
            });
            $view->with('webshopCategories', $categories);
        });
    }
}

