<?php

use App\Models\Category;
use App\Models\ContentBlock;
use App\Models\Product;
use App\Models\User;

function makeTrustCategory(): Category
{
    return Category::firstOrCreate(
        ['slug' => 'trustcat'],
        ['name' => 'Trustcat', 'status' => true, 'sort_order' => 0]
    );
}

it('shows the CMS trust bar on the webshop category page', function () {
    $cat = makeTrustCategory();
    Product::create([
        'category_id' => $cat->id, 'title' => 'Trust Laptop', 'price' => 500,
        'stock_status' => 'in_stock', 'status' => true,
    ]);

    $this->get('/webshop/' . $cat->slug)
        ->assertOk()
        ->assertSee('Gratis verzending', false)
        ->assertSee('Afhalen in Apeldoorn', false)
        ->assertSee('Veilig betalen', false);
});

it('renders CMS-edited trust items on the webshop page', function () {
    $cat = makeTrustCategory();
    Product::create([
        'category_id' => $cat->id, 'title' => 'Trust Laptop', 'price' => 500,
        'stock_status' => 'in_stock', 'status' => true,
    ]);

    ContentBlock::updateOrCreate(
        ['page' => 'productinfo', 'section' => 'info', 'block_key' => 'webshop_trust'],
        ['type' => 'json', 'json_value' => [
            ['icon' => 'rocket', 'title' => 'Supersnelle levering', 'subtitle' => 'Voor 23:59 besteld'],
        ]]
    );
    \App\Support\Cms::bust();

    $this->get('/webshop/' . $cat->slug)
        ->assertOk()
        ->assertSee('Supersnelle levering', false)
        ->assertDontSee('Gratis verzending', false);
});

it('shows identical CMS trust texts on cart with and without products', function () {
    $cat = makeTrustCategory();
    $product = Product::create([
        'category_id' => $cat->id, 'title' => 'Trust Laptop', 'price' => 500,
        'stock_status' => 'in_stock', 'status' => true,
    ]);

    ContentBlock::updateOrCreate(
        ['page' => 'productinfo', 'section' => 'info', 'block_key' => 'webshop_trust'],
        ['type' => 'json', 'json_value' => [
            ['icon' => 'rocket', 'title' => 'Unieke leverbelofte', 'subtitle' => 'Alleen in CMS'],
        ]]
    );
    \App\Support\Cms::bust();

    // Empty cart — no nested page container around the strip
    $this->get('/cart')
        ->assertOk()
        ->assertSee('Unieke leverbelofte', false)
        ->assertDontSee('max-w-[1450px]', false);

    // Non-empty cart
    $cart = \App\Models\Cart::create(['cart_token' => (string) \Illuminate\Support\Str::uuid()]);
    app(\App\Services\CartService::class)->addItem($cart, $product, 1);

    $this->withCookie(\App\Services\CartService::COOKIE_NAME, $cart->cart_token)
        ->get('/cart')
        ->assertOk()
        ->assertSee('Unieke leverbelofte', false)
        ->assertDontSee('max-w-[1450px]', false);
});

it('shows the webshop trust block in the productinfo admin editor', function () {
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    $this->actingAs($u)
        ->get('/admin/content/productinfo/section/info')
        ->assertOk()
        ->assertSee('Webshop vertrouwensbalk', false);
});
