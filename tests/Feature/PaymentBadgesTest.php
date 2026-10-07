<?php

use App\Models\Cart;
use App\Models\Category;
use App\Models\ContentBlock;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Support\Cms;
use Illuminate\Support\Str;

function cartWithBadgeProduct(): Cart
{
    $category = Category::firstOrCreate(
        ['slug' => 'badgecat'],
        ['name' => 'Badgecat', 'status' => true, 'sort_order' => 0]
    );
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Badge Laptop',
        'price' => 100.00, 'stock_status' => 'in_stock', 'status' => true,
    ]);
    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, 1);

    return $cart->fresh();
}

it('shows default payment badges on cart and checkout', function () {
    $cart = cartWithBadgeProduct();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->get('/cart')
        ->assertOk()
        ->assertSee('iDEAL', false)
        ->assertSee('Bancontact', false)
        ->assertSee('VISA', false);
});

it('renders uploaded badge logos from CMS on cart and checkout', function () {
    ContentBlock::updateOrCreate(
        ['page' => 'productinfo', 'section' => 'info', 'block_key' => 'payment_badges'],
        ['type' => 'json', 'json_value' => [
            ['image' => 'assets/img/landing/ideal-logo.webp', 'label' => 'iDEAL'],
            ['image' => null, 'label' => 'Bancontact'],
        ]]
    );
    Cms::bust();
    $cart = cartWithBadgeProduct();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->get('/cart')
        ->assertOk()
        ->assertSee('assets/img/landing/ideal-logo.webp', false)
        ->assertSee('Bancontact', false);
});

it('saves a json-row logo upload for payment badges', function () {
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    $logo = \Illuminate\Http\UploadedFile::fake()->image('ideal-logo.png', 200, 80);

    $res = $this->actingAs($u)->post('/admin/content/productinfo/section/info', [
        'blocks' => [
            'payment_badges' => [
                ['image' => '', 'image_file' => $logo, 'label' => 'iDEAL'],
                ['image' => '', 'label' => 'Bancontact'],
            ],
        ],
    ])->assertOk();

    $saved = $res->json('saved.payment_badges');
    expect($saved[0]['image'])->toStartWith('assets/img/landing/');
    expect($saved[0]['label'])->toBe('iDEAL');
    expect(file_exists(public_path($saved[0]['image'])))->toBeTrue();

    // Cleanup the uploaded test file
    @unlink(public_path($saved[0]['image']));

    $stored = \App\Models\ContentBlock::where('page', 'productinfo')
        ->where('section', 'info')->where('block_key', 'payment_badges')->first();
    expect($stored)->not->toBeNull();
});

it('shows the payment badges block in the productinfo admin editor', function () {
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    $this->actingAs($u)
        ->get('/admin/content/productinfo/section/info')
        ->assertOk()
        ->assertSee('Betaalmethodes', false);
});
