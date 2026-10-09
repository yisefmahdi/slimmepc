<?php

/*
 * Sub-agent F (testing audit): webshop CRUD, validation, coupons, cart,
 * checkout totals, mocked Mollie webhook and technician quote flows.
 * Never hits real Mollie/SMTP: MolliePaymentService is mocked, mail is faked.
 */

use App\Mail\OrderInvoiceMail;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\LicenseCode;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ShippingRate;
use App\Models\TechnicianForm;
use App\Models\User;
use App\Services\CartService;
use App\Services\Payments\MolliePaymentService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

beforeEach(function () {
    $this->withCredentials();
    foreach (app('router')->getRoutes() as $route) {
        $route->controller = null;
    }
});

function auditShopAdmin(): User
{
    $u = User::factory()->create();
    $u->role = 'admin';
    $u->save();

    return $u;
}

function auditShopCategory(string $name = 'Audit Laptops'): Category
{
    return Category::create(['name' => $name, 'status' => true, 'sort_order' => 0]);
}

function auditShopProduct(Category $category, array $overrides = []): Product
{
    return Product::create(array_merge([
        'category_id' => $category->id,
        'title' => 'Audit Product '.uniqid(),
        'price' => 24.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'description' => 'Test',
    ], $overrides));
}

function auditShopGuestCart(Product $product, int $qty = 1): Cart
{
    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, $qty);

    return $cart->fresh();
}

function auditShopCheckoutPayload(array $overrides = []): array
{
    return array_merge([
        'email' => 'klant@voorbeeld.nl',
        'first_name' => 'Jan',
        'last_name' => 'Jansen',
        'street' => 'Hoofdstraat',
        'house_number' => '12',
        'postcode' => '1234 AB',
        'city' => 'Apeldoorn',
        'country' => 'Nederland',
        'phone' => '0612345678',
        'shipping_method' => 'delivery',
        'terms' => true,
    ], $overrides);
}

/** Fake Mollie payment payload as returned by the (mocked) gateway. */
function auditFakeMolliePayment(int $orderId, string $status, string $amount): Payment
{
    $p = new Payment(new MollieApiClient);
    $p->id = 'tr_audit123';
    $p->status = $status;
    $p->method = 'ideal';
    $p->amount = (object) ['currency' => 'EUR', 'value' => $amount];
    $p->metadata = (object) ['order_id' => $orderId];

    return $p;
}

function auditMockMollie(Payment $payment, bool $paid, bool $amountOk): void
{
    $mock = Mockery::mock(MolliePaymentService::class);
    $mock->shouldReceive('isConfigured')->andReturn(true);
    $mock->shouldReceive('getPayment')->andReturn($payment);
    $mock->shouldReceive('isPaid')->andReturn($paid);
    $mock->shouldReceive('amountMatches')->andReturn($amountOk);
    app()->instance(MolliePaymentService::class, $mock);
    foreach (app('router')->getRoutes() as $route) {
        $route->controller = null;
    }
}

// ---------- category CRUD + validation ----------

it('manages categories with validation and guards', function () {
    $admin = auditShopAdmin();

    $this->actingAs($admin)->postJson('/admin/webshop/categories', [
        'name' => 'Gaming', 'status' => true,
    ])->assertStatus(201)->assertJsonPath('category.slug', 'gaming');

    // Duplicate + missing name.
    $this->actingAs($admin)->postJson('/admin/webshop/categories', [
        'name' => 'Gaming', 'status' => true,
    ])->assertStatus(422)->assertJsonValidationErrors('name');
    $this->actingAs($admin)->postJson('/admin/webshop/categories', ['status' => true])
        ->assertStatus(422)->assertJsonValidationErrors('name');

    $category = Category::where('name', 'Gaming')->firstOrFail();
    $product = auditShopProduct($category);

    // Guards: no deactivation or deletion while products are linked.
    $this->actingAs($admin)->putJson('/admin/webshop/categories/'.$category->id, [
        'name' => 'Gaming', 'status' => false,
    ])->assertStatus(422);
    $this->actingAs($admin)->deleteJson('/admin/webshop/categories/'.$category->id)->assertStatus(422);
    $this->actingAs($admin)->postJson('/admin/webshop/categories/'.$category->id.'/toggle', ['status' => false])
        ->assertStatus(422);
    $this->actingAs($admin)->postJson('/admin/webshop/categories/'.$category->id.'/toggle', [])
        ->assertStatus(422)->assertJsonValidationErrors('status');

    // Rename works and refreshes the slug.
    $this->actingAs($admin)->putJson('/admin/webshop/categories/'.$category->id, [
        'name' => 'Gaming Pro', 'status' => true,
    ])->assertOk()->assertJsonPath('category.slug', 'gaming-pro');

    // Empty category can be toggled and deleted.
    $empty = Category::create(['name' => 'Leeg', 'status' => true, 'sort_order' => 1]);
    $this->actingAs($admin)->postJson('/admin/webshop/categories/'.$empty->id.'/toggle', ['status' => false])->assertOk();
    $this->actingAs($admin)->deleteJson('/admin/webshop/categories/'.$empty->id)->assertOk();
    $this->assertDatabaseMissing('categories', ['id' => $empty->id]);

    // Non-admins are blocked.
    $this->actingAs(User::factory()->create())->postJson('/admin/webshop/categories', ['name' => 'X', 'status' => true])->assertForbidden();
});

// ---------- product CRUD + validation ----------

it('manages products with validation, toggles and sanitization', function () {
    $admin = auditShopAdmin();
    $category = auditShopCategory();

    $payload = [
        'category_id' => $category->id, 'title' => 'Audit Laptop Pro',
        'price' => 999.00, 'status' => true,
        'description' => '<p>Mooi</p><script>alert(1)</script>',
        'colors' => ['#FF0000', 'not-a-color'],
    ];

    $res = $this->actingAs($admin)->postJson('/admin/webshop/products', $payload)->assertStatus(201);
    $product = Product::findOrFail($res->json('product.id'));
    expect($product->slug)->toBe('audit-laptop-pro')
        ->and($product->description)->not->toContain('<script>')
        ->and($product->colors)->toBe(['#FF0000']);

    // Validation: missing/duplicate/negative/bad dates/bad url/malicious junk.
    $this->actingAs($admin)->postJson('/admin/webshop/products', ['category_id' => $category->id, 'price' => 10, 'status' => true])
        ->assertStatus(422)->assertJsonValidationErrors('title');
    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $category->id, 'title' => 'Audit Laptop Pro', 'price' => 10, 'status' => true,
    ])->assertStatus(422)->assertJsonValidationErrors('title');
    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $category->id, 'title' => 'Negatief', 'price' => -5, 'status' => true,
    ])->assertStatus(422)->assertJsonValidationErrors('price');
    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $category->id, 'title' => 'Datums', 'price' => 10, 'status' => true,
        'discount_type' => 'percentage', 'discount_value' => 10,
        'discount_start_date' => '2026-05-02', 'discount_end_date' => '2026-05-01',
    ])->assertStatus(422)->assertJsonValidationErrors('discount_end_date');
    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $category->id, 'title' => 'Urltje', 'price' => 10, 'status' => true,
        'download_64bit_url' => 'javascript:alert(1)',
    ])->assertStatus(422)->assertJsonValidationErrors('download_64bit_url');
    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => 999999, 'title' => 'Spook', 'price' => 10, 'status' => true,
    ])->assertStatus(422)->assertJsonValidationErrors('category_id');

    // Update + toggles + delete.
    $this->actingAs($admin)->putJson('/admin/webshop/products/'.$product->id, [
        'category_id' => $category->id, 'title' => 'Audit Laptop Pro', 'price' => 899.00, 'status' => true,
    ])->assertOk();
    expect($product->fresh()->price)->toBe('899.00');

    $this->actingAs($admin)->postJson('/admin/webshop/products/'.$product->id.'/toggle', ['status' => false])->assertOk();
    expect($product->fresh()->status)->toBeFalse();
    $this->actingAs($admin)->postJson('/admin/webshop/products/'.$product->id.'/toggle-featured', ['is_featured' => true])->assertOk();
    expect($product->fresh()->is_featured)->toBeTrue();

    $this->actingAs($admin)->deleteJson('/admin/webshop/products/'.$product->id)->assertOk();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

// ---------- coupon CRUD + validation ----------

it('manages coupons with validation', function () {
    $admin = auditShopAdmin();
    $payload = [
        'code' => 'audit10', 'discount_type' => 'percentage', 'discount_value' => 10, 'status' => true,
    ];

    $res = $this->actingAs($admin)->postJson('/admin/webshop/coupons', $payload)->assertStatus(201);
    expect($res->json('coupon.code'))->toBe('AUDIT10');

    $this->actingAs($admin)->postJson('/admin/webshop/coupons', $payload)->assertStatus(422)->assertJsonValidationErrors('code');
    $this->actingAs($admin)->postJson('/admin/webshop/coupons', [
        'code' => 'teveel', 'discount_type' => 'percentage', 'discount_value' => 150, 'status' => true,
    ])->assertStatus(422);
    $this->actingAs($admin)->postJson('/admin/webshop/coupons', [
        'code' => 'baddates', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true,
        'start_date' => '2026-05-02 10:00', 'end_date' => '2026-05-01 10:00',
    ])->assertStatus(422)->assertJsonValidationErrors('end_date');

    $coupon = Coupon::where('code', 'AUDIT10')->firstOrFail();
    $this->actingAs($admin)->postJson('/admin/webshop/coupons/'.$coupon->id.'/toggle', ['status' => false])->assertOk();
    expect($coupon->fresh()->status)->toBeFalse();

    $this->actingAs($admin)->putJson('/admin/webshop/coupons/'.$coupon->id, [
        'code' => 'AUDIT10', 'discount_type' => 'fixed', 'discount_value' => 7.50, 'status' => true,
    ])->assertOk()->assertJsonPath('coupon.discount_value', '7.50');

    $this->actingAs($admin)->deleteJson('/admin/webshop/coupons/'.$coupon->id)->assertOk();
    $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
});

// ---------- cart coupon logic ----------

it('applies coupons only when valid and removes them', function () {
    $category = auditShopCategory('Coupon Cat');
    $product = auditShopProduct($category, ['title' => 'Coupon Product '.uniqid(), 'price' => 100.00]);
    $cart = auditShopGuestCart($product);
    $cookie = [CartService::COOKIE_NAME => $cart->cart_token];

    $valid = Coupon::create(['code' => 'GELDIG10', 'discount_type' => 'percentage', 'discount_value' => 10, 'status' => true]);

    $res = $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'geldig10'])->assertOk();
    expect((float) $res->json('coupon.discount'))->toBe(10.0);

    // Unknown / inactive / expired / future / maxed / min-amount.
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'BESTAATNIET'])->assertStatus(422)->assertJsonFragment(['message' => 'Ongeldige kortingscode.']);
    $inactive = Coupon::create(['code' => 'INACTIEF', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => false]);
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'INACTIEF'])->assertStatus(422);
    $expired = Coupon::create(['code' => 'OUD', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true, 'end_date' => now()->subDay()]);
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'OUD'])->assertStatus(422);
    $future = Coupon::create(['code' => 'TOEKOMST', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true, 'start_date' => now()->addDay()]);
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'TOEKOMST'])->assertStatus(422);
    $maxed = Coupon::create(['code' => 'VOL', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true, 'usage_limit' => 1, 'used_count' => 1]);
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'VOL'])->assertStatus(422);
    $min = Coupon::create(['code' => 'MIN200', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true, 'min_amount' => 200]);
    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'MIN200'])
        ->assertStatus(422)
        ->assertJsonPath('message', fn ($m) => str_contains((string) $m, 'Minimaal bestelbedrag'));

    // Coupon code casing is normalized on write.
    expect($valid->fresh()->code)->toBe('GELDIG10');

    $this->withCookies($cookie)->deleteJson('/cart/coupon')->assertOk();
    expect($cart->fresh()->coupon_id)->toBeNull();
});

it('rejects reusing a single-use coupon', function () {
    $user = User::factory()->create();
    $category = auditShopCategory('Single Cat');
    $product = auditShopProduct($category, ['title' => 'Single Product '.uniqid(), 'price' => 50.00]);
    $cart = Cart::create(['user_id' => $user->id]);
    app(CartService::class)->addItem($cart, $product, 1);
    $coupon = Coupon::create(['code' => 'EENMALIG', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true, 'is_single_use' => true]);
    CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $user->id, 'used_at' => now()]);

    $this->actingAs($user)->postJson('/cart/coupon', ['code' => 'EENMALIG'])
        ->assertStatus(422)->assertJsonFragment(['message' => 'Je hebt deze code al gebruikt.']);
});

it('persists coupons into checkout totals and orders', function () {
    config(['services.mollie.key' => '']);
    $category = auditShopCategory('Checkout Coupon Cat');
    $product = auditShopProduct($category, ['title' => 'Coupon Checkout '.uniqid(), 'price' => 100.00]);
    $cart = auditShopGuestCart($product);
    Coupon::create(['code' => 'VAST5', 'discount_type' => 'fixed', 'discount_value' => 5, 'status' => true]);
    $cookie = [CartService::COOKIE_NAME => $cart->cart_token];

    $this->withCookies($cookie)->postJson('/cart/coupon', ['code' => 'VAST5'])->assertOk();

    $totals = $this->withCookies($cookie)->postJson('/checkout/totals', ['shipping_method' => 'delivery'])->assertOk()->json();
    expect((float) $totals['discount'])->toBe(5.0);

    $this->withCookies($cookie)->postJson('/checkout', auditShopCheckoutPayload())->assertStatus(201);
    $order = Order::latest('id')->first();
    expect($order->discount_code)->toBe('VAST5')
        ->and((float) $order->discount_amount)->toBe(5.0);
});

// ---------- cart validation + isolation ----------

it('validates cart mutations and isolates guests from each other', function () {
    $category = auditShopCategory('Cart Cat');
    $product = auditShopProduct($category, ['title' => 'Cart Product '.uniqid(), 'price' => 30.00]);
    $cartA = auditShopGuestCart($product);
    $itemId = $cartA->items()->first()->id;

    $this->postJson('/cart/items', [])->assertStatus(422)->assertJsonValidationErrors('product_id');
    $this->postJson('/cart/items', ['product_id' => 999999])->assertStatus(422)->assertJsonValidationErrors('product_id');
    $this->withCookies([CartService::COOKIE_NAME => $cartA->cart_token])
        ->postJson('/cart/items', ['product_id' => $product->id, 'quantity' => 0])->assertStatus(422);
    $this->withCookies([CartService::COOKIE_NAME => $cartA->cart_token])
        ->postJson('/cart/items', ['product_id' => $product->id, 'quantity' => 100])->assertStatus(422);

    // Guest B cannot touch guest A's line (404, not 403/200).
    $cartB = Cart::create(['cart_token' => (string) Str::uuid()]);
    $this->withCookies([CartService::COOKIE_NAME => $cartB->cart_token])
        ->patchJson('/cart/items/'.$itemId, ['quantity' => 5])->assertNotFound();
    $this->withCookies([CartService::COOKIE_NAME => $cartB->cart_token])
        ->deleteJson('/cart/items/'.$itemId)->assertNotFound();

    // Owner can update within bounds.
    $this->withCookies([CartService::COOKIE_NAME => $cartA->cart_token])
        ->patchJson('/cart/items/'.$itemId, ['quantity' => 2])->assertOk();
    $this->withCookies([CartService::COOKIE_NAME => $cartA->cart_token])
        ->patchJson('/cart/items/'.$itemId, ['quantity' => 0])->assertStatus(422);
});

// ---------- checkout validation boundaries ----------

it('validates checkout boundaries in Dutch', function () {
    $category = auditShopCategory('Boundary Cat');
    $product = auditShopProduct($category, ['title' => 'Boundary '.uniqid()]);
    $cart = auditShopGuestCart($product);
    $cookie = [CartService::COOKIE_NAME => $cart->cart_token];

    $base = auditShopCheckoutPayload();
    $post = fn (array $over) => $this->withCookies($cookie)->postJson('/checkout', array_merge($base, $over));

    $post(['postcode' => '12345'])->assertStatus(422)->assertJsonValidationErrors('postcode');
    $post(['postcode' => 'ABCD EF'])->assertStatus(422)->assertJsonValidationErrors('postcode');
    $post(['phone' => '123'])->assertStatus(422)->assertJsonValidationErrors('phone');
    $post(['first_name' => 'J'])->assertStatus(422)->assertJsonValidationErrors('first_name');
    $post(['email' => 'geen-email'])->assertStatus(422)->assertJsonValidationErrors('email');
    $post(['shipping_method' => 'drone'])->assertStatus(422)->assertJsonValidationErrors('shipping_method');
    $post(['terms' => false])->assertStatus(422)->assertJsonValidationErrors('terms');

    // Lowercase postcode without space is normalized and accepted.
    $post(['postcode' => '1234ab', 'email' => 'ok1@voorbeeld.nl'])->assertStatus(201);
});

// ---------- reviews ----------

it('validates public reviews and moderates them as admin', function () {
    $admin = auditShopAdmin();
    $category = auditShopCategory('Review Cat');
    $product = auditShopProduct($category, ['title' => 'Review Product '.uniqid()]);
    $url = '/webshop/'.$category->slug.'/'.$product->slug.'/reviews';

    $this->postJson($url, [
        'rating' => 5, 'body' => 'Hele goede laptop, snelle levering!', 'guest_name' => 'Piet', 'guest_email' => 'piet@example.com',
    ])->assertStatus(201);
    $this->postJson($url, ['rating' => 6, 'body' => 'Hele goede laptop, snelle levering!', 'guest_name' => 'Piet', 'guest_email' => 'piet@example.com'])
        ->assertStatus(422)->assertJsonValidationErrors('rating');
    $this->postJson($url, ['rating' => 4, 'body' => 'Te kort', 'guest_name' => 'Piet', 'guest_email' => 'piet@example.com'])
        ->assertStatus(422)->assertJsonValidationErrors('body');
    $this->postJson($url, ['rating' => 4, 'body' => 'Hele goede laptop, snelle levering!', 'guest_name' => 'Piet'])
        ->assertStatus(422)->assertJsonValidationErrors('guest_email');
    $this->postJson('/webshop/'.$category->slug.'/spook-product/reviews', ['rating' => 5, 'body' => 'Hele goede laptop, snelle levering!', 'guest_name' => 'Piet', 'guest_email' => 'piet@example.com'])
        ->assertNotFound();

    $review = ProductReview::latest('id')->firstOrFail();
    $this->actingAs($admin)->postJson('/admin/webshop/reviews/'.$review->id.'/reject')->assertOk();
    expect($review->fresh()->is_approved)->toBeFalse();
    $this->actingAs($admin)->postJson('/admin/webshop/reviews/'.$review->id.'/approve')->assertOk();
    expect($review->fresh()->is_approved)->toBeTrue();
    $this->actingAs(User::factory()->create())->postJson('/admin/webshop/reviews/'.$review->id.'/approve')->assertForbidden();
    $this->actingAs($admin)->deleteJson('/admin/webshop/reviews/'.$review->id)->assertOk();
    $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
});

// ---------- Mollie webhook (mocked) ----------

function auditPendingOrder(float $total = 30.95): Order
{
    $address = Address::create([
        'first_name' => 'Jan', 'last_name' => 'Jansen', 'street' => 'Hoofdstraat',
        'house_number' => '12', 'postcode' => '1234AB', 'city' => 'Apeldoorn',
        'country' => 'Nederland', 'phone' => '0612345678', 'email' => 'klant@voorbeeld.nl',
    ]);

    return Order::create([
        'billing_address_id' => $address->id, 'shipping_address_id' => $address->id,
        'customer_email' => 'klant@voorbeeld.nl', 'customer_phone' => '0612345678',
        'subtotal' => 25.58, 'tax_percentage' => 21, 'tax_amount' => 5.37,
        'shipping_method' => 'delivery', 'shipping_cost' => 6.95,
        'total_price' => $total, 'payment_status' => 'pending', 'order_status' => 'pending',
    ]);
}

it('finalizes a paid order through the mocked webhook exactly once', function () {
    Storage::fake('local');
    Mail::fake();
    $order = auditPendingOrder();
    auditMockMollie(auditFakeMolliePayment($order->id, 'paid', '30.95'), true, true);

    $this->postJson('/payment/webhook', ['id' => 'tr_audit123'])->assertOk()->assertJsonPath('status', 'ok');
    (fn () => $this->terminatingCallbacks = [])->call($this->app);

    // Duplicate delivery (Mollie retries) must be a no-op.
    $this->postJson('/payment/webhook', ['id' => 'tr_audit123'])->assertOk()->assertJsonPath('status', 'ok');
    (fn () => $this->terminatingCallbacks = [])->call($this->app);

    expect($order->fresh()->payment_status)->toBe('paid')
        ->and(OrderInvoice::where('order_id', $order->id)->count())->toBe(1);
    expect(Storage::disk('local')->exists(OrderInvoice::where('order_id', $order->id)->first()->pdf_path))->toBeTrue();
    Mail::assertSent(OrderInvoiceMail::class, 1);
});

it('marks failed payments and rejects amount mismatches through the webhook', function () {
    $failed = auditPendingOrder();
    auditMockMollie(auditFakeMolliePayment($failed->id, 'failed', '30.95'), false, true);
    $this->postJson('/payment/webhook', ['id' => 'tr_x'])->assertOk()->assertJsonPath('status', 'ok');
    (fn () => $this->terminatingCallbacks = [])->call($this->app);
    expect($failed->fresh()->payment_status)->toBe('failed')
        ->and(OrderInvoice::where('order_id', $failed->id)->count())->toBe(0);

    $mismatch = auditPendingOrder();
    auditMockMollie(auditFakeMolliePayment($mismatch->id, 'paid', '1.00'), true, false);
    $this->postJson('/payment/webhook', ['id' => 'tr_x'])->assertOk()->assertJsonPath('status', 'amount-mismatch');
    (fn () => $this->terminatingCallbacks = [])->call($this->app);
    expect($mismatch->fresh()->payment_status)->toBe('failed')
        ->and(OrderInvoice::where('order_id', $mismatch->id)->count())->toBe(0);
});

it('handles webhook edge cases without exceptions', function () {
    config(['services.mollie.key' => '']);

    // No Mollie key or id: ignored, still 200 (Mollie must not retry).
    $this->postJson('/payment/webhook', [])->assertOk()->assertJsonPath('status', 'ignored');

    config(['services.mollie.key' => 'test_123456789012345678901234567890']);
    // Unknown order id inside metadata: 200 unknown-order.
    auditMockMollie(auditFakeMolliePayment(999999, 'paid', '10.00'), true, true);
    $this->postJson('/payment/webhook', ['id' => 'tr_x'])->assertOk()->assertJsonPath('status', 'unknown-order');

    // Gateway errors: 500 retry so Mollie redelivers.
    $mock = Mockery::mock(MolliePaymentService::class);
    $mock->shouldReceive('isConfigured')->andReturn(true);
    $mock->shouldReceive('getPayment')->andThrow(new RuntimeException('gateway down'));
    app()->instance(MolliePaymentService::class, $mock);
    foreach (app('router')->getRoutes() as $route) {
        $route->controller = null;
    }

    $this->postJson('/payment/webhook', ['id' => 'tr_x'])->assertStatus(500)->assertJsonPath('status', 'retry');
});

it('assigns each licence code only once even on double finalize', function () {
    Storage::fake('local');
    Mail::fake();
    $category = auditShopCategory('Licence Race Cat');
    $product = auditShopProduct($category, ['title' => 'Licence Race '.uniqid(), 'price' => 60.00, 'is_digital' => true]);
    $code = LicenseCode::create(['product_id' => $product->id, 'code' => 'RACE-'.uniqid(), 'status' => 'available']);

    $order = auditPendingOrder(60.00);
    $item = $order->items()->create([
        'product_id' => $product->id, 'product_name' => $product->title,
        'product_price' => 60.00, 'quantity' => 1, 'total_price' => 60.00,
    ]);
    auditMockMollie(auditFakeMolliePayment($order->id, 'paid', '60.00'), true, true);

    $this->postJson('/payment/webhook', ['id' => 'tr_a'])->assertOk();
    (fn () => $this->terminatingCallbacks = [])->call($this->app);
    $this->postJson('/payment/webhook', ['id' => 'tr_a'])->assertOk();
    (fn () => $this->terminatingCallbacks = [])->call($this->app);

    expect($code->fresh()->status)->toBe('sold')
        ->and($code->fresh()->order_item_id)->toBe($item->id)
        ->and(OrderInvoice::where('order_id', $order->id)->count())->toBe(1)
        ->and(LicenseCode::where('product_id', $product->id)->where('status', 'sold')->count())->toBe(1);
});

it('creates a Mollie payment on checkout when the gateway is configured', function () {
    config(['services.mollie.key' => 'test_123456789012345678901234567890']);
    ShippingRate::firstOrCreate(['slug' => 'delivery'], [
        'name' => 'Standaard verzending',
        'price' => 6.95,
        'free_above' => 75.00,
        'is_active' => true,
        'sort_order' => 0,
    ]);
    $category = auditShopCategory('Mollie Cat');
    $product = auditShopProduct($category, ['title' => 'Mollie Product '.uniqid(), 'price' => 24.00]);
    $cart = auditShopGuestCart($product);

    $fakePayment = new Payment(new MollieApiClient);
    $fakePayment->id = 'tr_checkout1';
    $fakePayment->_links = (object) ['checkout' => (object) ['href' => 'https://www.mollie.com/checkout/test-redirect']];

    $mock = Mockery::mock(MolliePaymentService::class);
    $mock->shouldReceive('isConfigured')->andReturn(true);
    $mock->shouldReceive('createPayment')->andReturn($fakePayment);
    app()->instance(MolliePaymentService::class, $mock);

    $res = $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->postJson('/checkout', auditShopCheckoutPayload())
        ->assertStatus(201);
    expect($res->json('redirect'))->toContain('mollie.com');
    expect(Order::latest('id')->first()->mollie_payment_id)->toBe('tr_checkout1');
});

// ---------- licence code admin ----------

it('manages licence codes with a sold-code deletion guard', function () {
    $admin = auditShopAdmin();
    $category = auditShopCategory('Code Cat');
    $product = auditShopProduct($category, ['title' => 'Code Product '.uniqid()]);

    $this->actingAs($admin)->postJson('/admin/webshop/license-codes', ['product_id' => $product->id])
        ->assertStatus(422)->assertJsonValidationErrors('code');
    $res = $this->actingAs($admin)->postJson('/admin/webshop/license-codes', [
        'product_id' => $product->id, 'code' => 'UNIEK-123',
    ])->assertStatus(201);
    $this->actingAs($admin)->postJson('/admin/webshop/license-codes', [
        'product_id' => $product->id, 'code' => 'UNIEK-123',
    ])->assertStatus(422);

    $code = LicenseCode::findOrFail($res->json('code.id'));
    $this->actingAs($admin)->deleteJson('/admin/webshop/license-codes/'.$code->id)->assertOk();

    $sold = LicenseCode::create(['product_id' => $product->id, 'code' => 'VERKOCHT-1', 'status' => 'sold', 'order_id' => 1]);
    $this->actingAs($admin)->deleteJson('/admin/webshop/license-codes/'.$sold->id)->assertStatus(422);
});

// ---------- technician quote ----------

it('quotes technician jobs and blocks outsiders', function () {
    $tech = User::factory()->create(['role' => 'technician']);
    $client = User::factory()->create();

    $res = $this->actingAs($tech)->postJson('/technician/quote', [
        'klantnummer' => $client->klantnummer, 'start_time' => '09:00', 'end_time' => '10:00',
    ])->assertOk();
    expect($res->json('success'))->toBeTrue()
        ->and($res->json('minutes'))->toBe(60)
        ->and($res->json('quarters'))->toBe(4);

    $this->actingAs($tech)->postJson('/technician/quote', [
        'klantnummer' => $client->klantnummer, 'start_time' => '10:00', 'end_time' => '09:00',
    ])->assertStatus(422);
    $this->actingAs($tech)->postJson('/technician/quote', [
        'klantnummer' => 'SLP-000000', 'start_time' => '09:00', 'end_time' => '10:00',
    ])->assertStatus(422);

    // Without a key the payment form stores nothing payable: 502, no form row.
    config(['services.mollie.key' => '']);
    $this->actingAs($tech)->postJson('/technician/payment/submit', [
        'klantnummer' => $client->klantnummer, 'start_time' => '09:00', 'end_time' => '10:00',
    ])->assertStatus(502);
    expect(TechnicianForm::count())->toBe(1); // form stored, payment pending config
});
