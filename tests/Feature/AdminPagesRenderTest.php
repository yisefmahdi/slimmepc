<?php

use App\Models\Order;
use App\Models\User;

function makeAdminRenderUser(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

it('renders the admin orders index', function () {
    $this->actingAs(makeAdminRenderUser())
        ->get('/admin/orders')
        ->assertOk()
        ->assertSee('Bestellingen');
});

it('renders the admin order show page', function () {
    $admin = makeAdminRenderUser();
    $order = Order::create([
        'customer_email' => 'klant@example.com',
        'customer_phone' => '0612345678',
        'subtotal' => 100,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);
    \App\Models\OrderInvoice::create([
        'order_id' => $order->id,
        'invoice_number' => 'INV-2026-TEST01',
        'invoice_date' => now()->toDateString(),
        'customer_name' => 'Jan Jansen',
        'customer_email' => 'klant@example.com',
        'subtotal' => 100,
        'tax_percentage' => 21,
        'tax_amount' => 6.95,
        'total' => 106.95,
    ]);

    $this->actingAs($admin)
        ->get('/admin/orders/'.$order->id)
        ->assertOk()
        ->assertSee($order->order_number)
        ->assertSee('PDF downloaden')
        ->assertDontSee('Opnieuw genereren')
        ->assertSee('data-invoice-download', false);
});

it('renders the admin shipping index', function () {
    $this->actingAs(makeAdminRenderUser())
        ->get('/admin/shipping')
        ->assertOk()
        ->assertSee('Verzendopties');
});

it('regenerates the admin order invoice pdf from the current template', function () {
    $admin = makeAdminRenderUser();
    $order = Order::create([
        'customer_email' => 'klant@example.com',
        'customer_phone' => '0612345678',
        'subtotal' => 100,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);
    \App\Models\OrderInvoice::create([
        'order_id' => $order->id,
        'invoice_number' => 'INV-2026-TEST02',
        'invoice_date' => now()->toDateString(),
        'customer_name' => 'Jan Jansen',
        'customer_email' => 'klant@example.com',
        'subtotal' => 100,
        'tax_percentage' => 21,
        'tax_amount' => 6.95,
        'total' => 106.95,
    ]);

    $this->actingAs($admin)
        ->get('/admin/orders/'.$order->id.'/invoice')
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect(\Illuminate\Support\Facades\Storage::disk('local')->exists('invoices/orders/INV-2026-TEST02.pdf'))->toBeTrue();
});
