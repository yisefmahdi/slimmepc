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

    $this->actingAs($admin)
        ->get('/admin/orders/'.$order->id)
        ->assertOk()
        ->assertSee($order->order_number);
});

it('renders the admin shipping index', function () {
    $this->actingAs(makeAdminRenderUser())
        ->get('/admin/shipping')
        ->assertOk()
        ->assertSee('Verzendopties');
});
