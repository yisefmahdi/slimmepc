<?php

/*
 * Sub-agent F (testing audit): application-backed unit-style tests for the
 * Cms helper, coupon activity windows and cart totals.
 */

use App\Models\Cart;
use App\Models\Category;
use App\Models\ContentBlock;
use App\Models\ContentMeta;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\CartService;
use App\Support\Cms;
use Illuminate\Support\Facades\Cache;

it('versions and caches cms pages', function () {
    Cache::flush();
    ContentMeta::where('meta_key', config('cms.cache_version_key'))->delete();
    expect(Cms::version())->toBe('1');

    ContentBlock::updateOrCreate(
        ['page' => 'home', 'section' => 'hero', 'block_key' => 'title_line1'],
        ['type' => 'text', 'value' => 'Hallo']
    );

    expect(Cms::get('home', 'hero', 'title_line1'))->toBe('Hallo');
    expect(Cms::get('home', 'hero', 'onbekend', 'fallback'))->toBe('fallback');

    $page = Cms::page('home');
    expect($page['hero']['title_line1'])->toBe('Hallo');

    $this->travel(2)->seconds();
    Cms::bust();
    expect(Cms::version())->not->toBe('1');

    ContentBlock::updateOrCreate(
        ['page' => 'home', 'section' => 'hero', 'block_key' => 'title_line1'],
        ['type' => 'text', 'value' => 'Wereld']
    );
    expect(Cms::get('home', 'hero', 'title_line1'))->toBe('Wereld');
});

it('flattens grouped design settings and keeps flat ones', function () {
    ContentMeta::updateOrCreate(['meta_key' => 'design'], [
        'meta_value' => json_encode(['site' => ['meta_title' => 'Groep Titel']]),
    ]);
    expect(Cms::designValue('meta_title'))->toBe('Groep Titel');

    ContentMeta::updateOrCreate(['meta_key' => 'design'], [
        'meta_value' => json_encode(['meta_title' => 'Platte Titel']),
    ]);
    Cms::bust();
    expect(Cms::designValue('meta_title'))->toBe('Platte Titel');
    expect(Cms::designValue('bestaat_niet', 'dflt'))->toBe('dflt');
});

it('evaluates coupon activity windows', function () {
    expect((new Coupon(['status' => false]))->isActive())->toBeFalse();
    expect((new Coupon(['status' => true, 'end_date' => now()->subDay()]))->isActive())->toBeFalse();
    expect((new Coupon(['status' => true, 'start_date' => now()->addDay()]))->isActive())->toBeFalse();
    expect((new Coupon(['status' => true, 'usage_limit' => 2, 'used_count' => 2]))->isActive())->toBeFalse();
    expect((new Coupon(['status' => true, 'usage_limit' => 2, 'used_count' => 1]))->isActive())->toBeTrue();
    expect((new Coupon(['status' => true]))->isActive())->toBeTrue();
    expect((new Coupon(['status' => true, 'usage_limit' => 2, 'used_count' => 1]))->isMaxedOut())->toBeFalse();
    expect((new Coupon(['status' => true, 'end_date' => now()->subDay()]))->isExpired())->toBeTrue();
});

it('totals a cart with inclusive vat', function () {
    $category = Category::create(['name' => 'Unit Cat', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Unit Product', 'price' => 24.00,
        'stock_status' => 'in_stock', 'status' => true, 'description' => 't',
    ]);
    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, 1);

    $totals = app(CartService::class)->totals($cart->fresh(), 'pickup');
    expect($totals['total'])->toBe(24.00)
        ->and($totals['shipping'])->toBe(0.0)
        ->and($totals['tax'])->toBe(round(24.00 * 21 / 121, 2));
});
