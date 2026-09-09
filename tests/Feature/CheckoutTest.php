<?php

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\Product;
use App\Models\ShippingRate;
use App\Services\CartService;
use App\Services\OrderPaymentService;

function makeCheckoutProduct(): Product
{
    ShippingRate::firstOrCreate(['slug' => 'delivery'], [
        'name' => 'Standaard verzending',
        'price' => 6.95,
        'free_above' => 75.00,
        'is_active' => true,
        'sort_order' => 0,
    ]);
    ShippingRate::firstOrCreate(['slug' => 'pickup'], [
        'name' => 'Afhalen in Apeldoorn',
        'price' => 0.00,
        'free_above' => null,
        'is_active' => true,
        'sort_order' => 1,
    ]);

    $category = Category::create([
        'name' => 'Testcat',
        'status' => true,
        'sort_order' => 0,
    ]);

    return Product::create([
        'category_id' => $category->id,
        'title' => 'Testlaptop Pro',
        'brand' => 'Testmerk',
        'price' => 24.00,
        'stock_status' => 'in_stock',
        'status' => true,
        'description' => 'Test',
    ]);
}

function guestCartWithProduct(): Cart
{
    $product = makeCheckoutProduct();
    $cart = Cart::create(['cart_token' => (string) Str::uuid()]);
    app(CartService::class)->addItem($cart, $product, 1);

    return $cart->fresh();
}

it('computes inclusive VAT and shipping from rates', function () {
    $cart = guestCartWithProduct();
    $service = app(CartService::class);

    // 24 incl, delivery 6.95 → total 30.95, tax = 30.95*21/121
    $totals = $service->totals($cart, 'delivery');
    expect($totals['shipping'])->toBe(6.95)
        ->and($totals['total'])->toBe(30.95)
        ->and($totals['tax'])->toBe(round(30.95 * 21 / 121, 2))
        ->and($totals['subtotal_excl'])->toBe(round(30.95 - $totals['tax'], 2));

    $pickup = $service->totals($cart, 'pickup');
    expect($pickup['shipping'])->toBe(0.0)
        ->and($pickup['total'])->toBe(24.00);
});

it('shows the checkout page for guests with a non-empty cart', function () {
    $cart = guestCartWithProduct();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->get('/checkout')
        ->assertOk()
        ->assertSee('Afrekenen')
        ->assertSee('Testlaptop Pro');
});

it('redirects to cart when the cart is empty', function () {
    $this->withCookie(CartService::COOKIE_NAME, (string) Str::uuid())
        ->get('/checkout')
        ->assertRedirect(route('cart.index'));
});

it('validates checkout input in Dutch', function () {
    $cart = guestCartWithProduct();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->postJson('/checkout', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'first_name', 'shipping_method']);
});

it('creates a pending order without Mollie key configured', function () {
    config(['services.mollie.key' => '']);
    $cart = guestCartWithProduct();

    $response = $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->postJson('/checkout', [
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
        ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', [
        'customer_email' => 'klant@voorbeeld.nl',
        'payment_status' => 'pending',
        'shipping_method' => 'delivery',
    ]);

    $order = Order::latest('id')->first();
    expect($order->order_number)->toStartWith('ORD-');
    expect((float) $order->total_price)->toBe(30.95);
    $this->assertDatabaseHas('order_items', ['order_id' => $order->id]);
    $this->assertDatabaseHas('addresses', ['id' => $order->billing_address_id]);
});

it('creates a pickup order without address fields', function () {
    config(['services.mollie.key' => '']);
    $cart = guestCartWithProduct();

    $this->withCookie(CartService::COOKIE_NAME, $cart->cart_token)
        ->withCredentials()
        ->postJson('/checkout', [
            'email' => 'klant@voorbeeld.nl',
            'first_name' => 'Jan',
            'last_name' => 'Jansen',
            'phone' => '0612345678',
            'shipping_method' => 'pickup',
        ])
        ->assertStatus(201);

    $order = Order::latest('id')->first();
    expect((float) $order->shipping_cost)->toBe(0.0)
        ->and((float) $order->total_price)->toBe(24.00);
});

it('finalizes a paid order idempotently with invoice and pdf', function () {
    Storage::fake('local');
    $product = makeCheckoutProduct();
    $address = Address::create([
        'first_name' => 'Jan', 'last_name' => 'Jansen', 'street' => 'Hoofdstraat',
        'house_number' => '12', 'postcode' => '1234AB', 'city' => 'Apeldoorn',
        'country' => 'Nederland', 'phone' => '0612345678', 'email' => 'klant@voorbeeld.nl',
    ]);
    $order = Order::create([
        'user_id' => null,
        'billing_address_id' => $address->id,
        'shipping_address_id' => $address->id,
        'klantnummer' => 'SLP-123456',
        'customer_email' => 'klant@voorbeeld.nl',
        'customer_phone' => '0612345678',
        'subtotal' => 100.00, 'tax_percentage' => 21, 'tax_amount' => 21.00,
        'discount_amount' => 0, 'shipping_method' => 'delivery', 'shipping_cost' => 0,
        'total_price' => 121.00, 'payment_status' => 'pending', 'order_status' => 'pending',
    ]);
    $order->items()->create([
        'product_id' => $product->id, 'product_name' => $product->title,
        'product_price' => 24.00, 'quantity' => 1, 'total_price' => 24.00,
    ]);

    $service = app(OrderPaymentService::class);
    $service->finalizeOrder($order->fresh());
    $service->finalizeOrder($order->fresh()); // second call must be a no-op

    expect(Order::find($order->id)->payment_status)->toBe('paid');
    expect(OrderInvoice::where('order_id', $order->id)->count())->toBe(1);
});
