<?php

use App\Mail\LicenseShortageMail;
use App\Models\Cart;
use App\Models\Category;
use App\Models\LicenseCode;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

function makeLicenseProduct(int $availableCodes = 0): Product
{
    $category = Category::create(['name' => 'Software', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id,
        'title' => 'Gated Office ' . Str::random(6),
        'price' => 60.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'is_digital' => true,
    ]);

    for ($i = 1; $i <= $availableCodes; $i++) {
        LicenseCode::create([
            'product_id' => $product->id,
            'code' => 'GATE-' . $product->id . '-' . $i . '-' . Str::random(4),
            'status' => 'available',
        ]);
    }
    // A sold code must never count toward availability
    LicenseCode::create([
        'product_id' => $product->id,
        'code' => 'GATE-SOLD-' . $product->id . '-' . Str::random(4),
        'status' => 'sold',
    ]);

    return $product->fresh();
}

function checkoutPayload(): array
{
    return [
        'email' => 'klant@voorbeeld.nl',
        'first_name' => 'Jan',
        'last_name' => 'Jansen',
        'phone' => '0612345678',
        'shipping_method' => 'pickup',
        'terms' => true,
    ];
}

it('refuses to add a digital product without available codes to the cart', function () {
    $product = makeLicenseProduct(0);

    $this->postJson('/cart/items', ['product_id' => $product->id, 'quantity' => 1])
        ->assertStatus(422)
        ->assertJsonPath('message', 'Dit digitale product is (tijdelijk) uitverkocht — er zijn geen licentiecodes meer beschikbaar.');
});

it('shows uitverkocht on the product page when the pool is empty', function () {
    $product = makeLicenseProduct(0);

    $this->get(route('webshop.product', [$product->category->slug, $product->slug]))
        ->assertOk()
        ->assertSee('Tijdelijk uitverkocht');
});

it('refuses checkout when the pool cannot cover the quantity', function () {
    $product = makeLicenseProduct(1);

    // qty 2 with only 1 available code
    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, 2);

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->postJson('/checkout', checkoutPayload())
        ->assertStatus(422)
        ->assertJsonPath('message', 'Een digitaal product in je winkelwagen is (tijdelijk) uitverkocht — er zijn geen licentiecodes meer beschikbaar.');
});

it('refuses raising cart quantity above the available pool', function () {
    $product = makeLicenseProduct(1);

    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, 1);
    $item = $cart->items()->first();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->patchJson('/cart/items/' . $item->id, ['quantity' => 3])
        ->assertStatus(422);
});

it('flips codes to sold with order links on successful finalize', function () {
    $product = makeLicenseProduct(2);
    $order = Order::create([
        'customer_email' => 'klant@voorbeeld.nl',
        'customer_phone' => '0612345678',
        'subtotal' => 120, 'total_price' => 120,
        'payment_status' => 'pending', 'order_status' => 'pending',
    ]);
    $item = $order->items()->create([
        'product_id' => $product->id, 'product_name' => $product->title,
        'product_price' => 60, 'quantity' => 2, 'total_price' => 120,
    ]);

    $shortages = app(OrderPaymentService::class)->assignLicenseCodes($order->fresh());

    expect($shortages)->toBe([]);
    expect(LicenseCode::where('order_item_id', $item->id)->count())->toBe(2);
    expect(LicenseCode::where('order_item_id', $item->id)->where('status', 'sold')->count())->toBe(2);
    expect(LicenseCode::where('order_item_id', $item->id)->where('order_id', $order->id)->count())->toBe(2);
});

it('flags shortage and renders the admin alert mail on partial pool', function () {
    $product = makeLicenseProduct(1);
    $order = Order::create([
        'customer_email' => 'klant@voorbeeld.nl',
        'customer_phone' => '0612345678',
        'subtotal' => 120, 'total_price' => 120,
        'payment_status' => 'pending', 'order_status' => 'pending',
    ]);
    $order->items()->create([
        'product_id' => $product->id, 'product_name' => $product->title,
        'product_price' => 60, 'quantity' => 2, 'total_price' => 120,
    ]);

    $shortages = app(OrderPaymentService::class)->assignLicenseCodes($order->fresh());

    expect($shortages)->toHaveCount(1);
    expect($shortages[0]['quantity'])->toBe(2);
    expect($shortages[0]['available'])->toBe(1);

    $html = (new LicenseShortageMail($order->fresh(), $shortages))->render();
    expect($html)->toContain('Licentiecodes op!')
        ->and($html)->toContain($order->order_number)
        ->and($html)->toContain($product->title);
});

it('counts only available codes toward saleability', function () {
    $product = makeLicenseProduct(0);

    expect($product->hasAvailableLicenses(1))->toBeFalse();

    LicenseCode::create(['product_id' => $product->id, 'code' => 'ONE-' . Str::random(6), 'status' => 'available']);

    expect($product->fresh()->hasAvailableLicenses(1))->toBeTrue();
    expect($product->fresh()->hasAvailableLicenses(2))->toBeFalse();
});
