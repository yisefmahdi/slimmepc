<?php

/*
 * Sub-agent F (testing audit): invoice generation, numbering, access control
 * and notification mail for webshop orders.
 */

use App\Mail\AdminOrderNotificationMail;
use App\Mail\OrderInvoiceMail;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\User;
use App\Services\OrderPaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

function auditInvoiceAdmin(): User
{
    $u = User::factory()->create();
    $u->role = 'admin';
    $u->save();

    return $u;
}

function auditInvoiceOrder(User $user, array $overrides = []): Order
{
    $address = Address::create([
        'user_id' => $user->id, 'first_name' => 'Jan', 'last_name' => 'Jansen',
        'street' => 'Hoofdstraat', 'house_number' => '12', 'postcode' => '1234AB',
        'city' => 'Apeldoorn', 'country' => 'Nederland', 'phone' => '0612345678',
        'email' => $user->email, 'type' => 'billing',
    ]);

    return Order::create(array_merge([
        'user_id' => $user->id,
        'billing_address_id' => $address->id,
        'shipping_address_id' => $address->id,
        'klantnummer' => $user->klantnummer,
        'customer_email' => $user->email,
        'customer_phone' => '0612345678',
        'subtotal' => 100.00, 'tax_percentage' => 21, 'tax_amount' => 17.36,
        'discount_amount' => 0, 'shipping_method' => 'delivery', 'shipping_cost' => 6.95,
        'total_price' => 106.95, 'payment_status' => 'pending', 'payment_method' => 'mollie',
        'order_status' => 'pending',
    ], $overrides));
}

it('generates a unique numbered invoice with pdf and mails on finalize', function () {
    Storage::fake('local');
    Mail::fake();
    config(['contact-inbox.notify_email' => 'admin@example.com']);

    $user = User::factory()->create();
    $order = auditInvoiceOrder($user);
    $order->items()->create([
        'product_id' => null, 'product_name' => 'Reparatie arbeid',
        'product_price' => 100.00, 'quantity' => 1, 'total_price' => 100.00,
    ]);

    app(OrderPaymentService::class)->finalizeOrder($order->fresh());
    $this->app->terminate();

    $invoice = OrderInvoice::where('order_id', $order->id)->firstOrFail();
    expect($invoice->invoice_number)->toStartWith('INV-'.date('Y').'-')
        ->and(Storage::disk('local')->exists($invoice->pdf_path))->toBeTrue()
        ->and($invoice->customer_email)->toBe($user->email)
        ->and((float) $invoice->total)->toBe(106.95);
    expect($order->fresh()->payment_status)->toBe('paid');

    Mail::assertSent(OrderInvoiceMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
    Mail::assertSent(AdminOrderNotificationMail::class, function ($mail) {
        return $mail->hasTo('admin@example.com');
    });
});

it('never duplicates invoice numbers', function () {
    Storage::fake('local');
    Mail::fake();

    $numbers = [];
    for ($i = 0; $i < 10; $i++) {
        $order = auditInvoiceOrder(User::factory()->create());
        $invoice = app(OrderPaymentService::class)->ensureInvoice($order);
        $numbers[] = $invoice->invoice_number;
    }

    expect($numbers)->toHaveCount(10);
    expect(array_unique($numbers))->toHaveCount(10);
    foreach ($numbers as $number) {
        expect($number)->toMatch('/^INV-\d{4}-[A-Z0-9]{6}$/');
    }
});

it('reuses the existing invoice and pdf on repeat finalize', function () {
    Storage::fake('local');
    Mail::fake();

    $order = auditInvoiceOrder(User::factory()->create());
    $service = app(OrderPaymentService::class);

    $first = $service->ensureInvoice($order->fresh());
    $again = $service->ensureInvoice($order->fresh());
    expect($again->id)->toBe($first->id);
    expect(OrderInvoice::where('order_id', $order->id)->count())->toBe(1);

    $withPdf = $service->ensurePdf($first);
    expect(Storage::disk('local')->exists($withPdf->pdf_path))->toBeTrue();
    $pathBefore = $withPdf->pdf_path;
    $second = $service->ensurePdf($withPdf->fresh());
    expect($second->pdf_path)->toBe($pathBefore);

    // Missing file regenerates at the same invoice number.
    Storage::disk('local')->delete($pathBefore);
    $regenerated = $service->ensurePdf($withPdf->fresh());
    expect($regenerated->invoice_number)->toBe($first->invoice_number)
        ->and(Storage::disk('local')->exists($regenerated->pdf_path))->toBeTrue();
});

it('enforces invoice download access control', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $tech = User::factory()->create(['role' => 'technician']);
    $order = auditInvoiceOrder($owner);
    app(OrderPaymentService::class)->ensureInvoice($order->fresh());

    // No pdf generated on this (non-faked) disk: owner gets 404, not a leak.
    $this->actingAs($owner)->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertNotFound();
    $this->actingAs($stranger)->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertNotFound();
    Auth::logout();
    $this->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertRedirect('/login');
    $this->actingAs($tech)->get('/admin/orders/'.$order->id.'/invoice')->assertForbidden();
    $this->actingAs($stranger)->get('/admin/orders/'.$order->id.'/invoice')->assertForbidden();
});

it('regenerates the admin invoice pdf from the current template', function () {
    Storage::fake('local');

    $owner = User::factory()->create();
    $order = auditInvoiceOrder($owner);
    $service = app(OrderPaymentService::class);
    $invoice = $service->ensureInvoice($order->fresh());
    $service->ensurePdf($invoice);

    $this->actingAs(auditInvoiceAdmin())->get('/admin/orders/'.$order->id.'/invoice')
        ->assertOk()
        ->assertHeader('content-disposition');
});

it('validates the admin order status endpoint', function () {
    Mail::fake();
    $admin = auditInvoiceAdmin();
    $order = auditInvoiceOrder(User::factory()->create());

    $this->actingAs($admin)->postJson('/admin/orders/'.$order->id.'/status', ['order_status' => 'shipped'])
        ->assertOk()->assertJsonPath('order_status', 'shipped');
    expect($order->fresh()->order_status)->toBe('shipped');

    $this->actingAs($admin)->postJson('/admin/orders/'.$order->id.'/status', ['order_status' => 'verdwenen'])
        ->assertStatus(422);
    $this->actingAs($admin)->postJson('/admin/orders/'.$order->id.'/status', [])
        ->assertStatus(422);
});
