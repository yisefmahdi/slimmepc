<?php

use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

function makeAccountOrder(User $user, array $overrides = []): Order
{
    return Order::create(array_merge([
        'user_id' => $user->id,
        'customer_email' => $user->email,
        'customer_phone' => '0612345678',
        'subtotal' => 100.00,
        'tax_amount' => 17.36,
        'shipping_method' => 'delivery',
        'shipping_cost' => 6.95,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'payment_method' => 'ideal',
        'order_status' => 'processing',
    ], $overrides));
}

it('redirects guests away from the order history', function () {
    $this->get('/mijn-bestellingen')->assertRedirect('/login');
});

it('shows an empty order history for new users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/mijn-bestellingen')
        ->assertOk()
        ->assertSee('Mijn bestellingen')
        ->assertSee('Nog geen bestellingen');
});

it('lists only the own orders', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $mine = makeAccountOrder($user);
    $theirs = makeAccountOrder($other);

    $this->actingAs($user)
        ->get('/mijn-bestellingen')
        ->assertOk()
        ->assertSee($mine->order_number)
        ->assertDontSee($theirs->order_number);
});

it('shows order details with items and totals', function () {
    $user = User::factory()->create();
    $order = makeAccountOrder($user);
    $order->items()->create([
        'product_id' => null,
        'product_name' => 'Test Laptop Pro',
        'product_price' => 100.00,
        'quantity' => 1,
        'total_price' => 100.00,
    ]);

    $this->actingAs($user)
        ->get('/mijn-bestellingen/'.$order->order_number)
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('Test Laptop Pro')
        ->assertSee('In behandeling');
});

it('blocks other users from viewing the order', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $order = makeAccountOrder($user);

    $this->actingAs($other)
        ->get('/mijn-bestellingen/'.$order->order_number)
        ->assertNotFound();
});

it('downloads the own invoice and blocks others', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $order = makeAccountOrder($user);

    Storage::disk('local')->put('invoices/orders/TEST-INV.pdf', 'fake-pdf');
    OrderInvoice::create([
        'order_id' => $order->id,
        'invoice_number' => 'INV-2026-TEST01',
        'invoice_date' => now()->toDateString(),
        'customer_name' => $user->name,
        'customer_email' => $user->email,
        'total' => 106.95,
        'pdf_path' => 'invoices/orders/TEST-INV.pdf',
    ]);

    $this->actingAs($user)
        ->get('/mijn-bestellingen/'.$order->order_number.'/factuur')
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=INV-2026-TEST01.pdf');

    $this->actingAs($other)
        ->get('/mijn-bestellingen/'.$order->order_number.'/factuur')
        ->assertNotFound();
});

it('returns 404 for the invoice when no pdf exists', function () {
    $user = User::factory()->create();
    $order = makeAccountOrder($user);

    $this->actingAs($user)
        ->get('/mijn-bestellingen/'.$order->order_number.'/factuur')
        ->assertNotFound();
});
