<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Favorite;
use App\Services\CartService;
use App\Services\InboundContactFetcher;
use App\Support\Cms;
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
        $this->app->singleton(CartService::class);
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

        // Share cart count with header (guest + auth)
        View::composer('landing.partials.header', function ($view) {
            try {
                $cartService = app(CartService::class);
                $count = $cartService->countForRequest(request());
            } catch (\Throwable $e) {
                $count = 0;
            }
            $view->with('cartCount', $count);
        });

        // Share wishlist count with header (auth only, guests see 0)
        View::composer('landing.partials.header', function ($view) {
            try {
                $user = request()->user();
                $count = $user ? Favorite::where('user_id', $user->id)->count() : 0;
            } catch (\Throwable $e) {
                $count = 0;
            }
            $view->with('wishlistCount', $count);
        });

        // Share CMS company data (logo, contact, copyright) with all email views —
        // same source as the website header/footer, so admin CMS edits update emails too
        View::composer('emails.*', function ($view) {
            try {
                $brand = Cms::page('home');
            } catch (\Throwable $e) {
                $brand = [];
            }
            $view->with('mailBrand', $brand);
        });
    }
}
