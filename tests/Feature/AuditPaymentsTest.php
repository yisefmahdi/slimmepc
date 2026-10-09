<?php

/**
 * Sub-agent E (Payments, Orders, Downloads & Business-Logic Abuse) regression tests.
 *
 * - SQLite :memory: only (phpunit.xml), Storage::fake(), Mail::fake().
 * - Mollie is NEVER hit: a fake MolliePaymentService is bound into the
 *   container; fake Payment resources are plain value objects (no HTTP).
 */

use App\Models\Address;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\DigitalFile;
use App\Models\LicenseCode;
use App\Models\Membership;
use App\Models\MembershipInvoice;
use App\Models\MembershipSetting;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\Product;
use App\Models\TechnicianForm;
use App\Models\TechnicianSetting;
use App\Models\User;
use App\Services\Payments\MolliePaymentService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

class AuditFakeMollie extends MolliePaymentService
{
    public static ?Payment $next = null;

    public function isConfigured(): bool
    {
        return true;
    }

    public function getPayment(string $paymentId): Payment
    {
        return self::$next ?? throw new RuntimeException('No fake Mollie payment queued');
    }

    public function createPayment(float $amount, string $description, string $redirectUrl, ?string $webhookUrl = null, array $metadata = []): Payment
    {
        // Never create real payments in tests: behave like "gateway down".
        throw new RuntimeException('Mollie disabled in audit tests');
    }
}

function auditPayMollie(): AuditFakeMollie
{
    $fake = new AuditFakeMollie;
    app()->instance(MolliePaymentService::class, $fake);

    return $fake;
}

function auditFakePayment(float $amount, array $metadata, string $status = 'paid'): Payment
{
    $p = new Payment(new MollieApiClient);
    $p->id = 'tr_audit'.random_int(1000, 9999);
    $p->status = $status;
    $p->paidAt = $status === 'paid' ? '2026-01-01T00:00:00+00:00' : null;
    $p->method = 'ideal';
    $p->amount = (object) ['currency' => 'EUR', 'value' => number_format($amount, 2, '.', '')];
    $p->metadata = (object) $metadata;

    return $p;
}

function auditPayAddress(): Address
{
    return Address::create([
        'first_name' => 'Jan', 'last_name' => 'Jansen', 'street' => 'Hoofdstraat',
        'house_number' => '12', 'postcode' => '1234AB', 'city' => 'Apeldoorn',
        'country' => 'Nederland', 'phone' => '0612345678', 'email' => 'klant@voorbeeld.nl',
    ]);
}

function auditPayOrder(float $total = 121.00, ?User $user = null): Order
{
    $address = auditPayAddress();
    $order = Order::create([
        'user_id' => $user?->id,
        'billing_address_id' => $address->id,
        'shipping_address_id' => $address->id,
        'klantnummer' => 'SLP-000001',
        'customer_email' => 'klant@voorbeeld.nl',
        'customer_phone' => '0612345678',
        'subtotal' => 100.00, 'tax_percentage' => 21, 'tax_amount' => 21.00,
        'discount_amount' => 0, 'shipping_method' => 'delivery', 'shipping_cost' => 0,
        'total_price' => $total, 'payment_status' => 'pending', 'order_status' => 'pending',
        'mollie_payment_id' => 'tr_audit_order',
    ]);

    return $order->fresh();
}

function auditPayAdmin(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

function auditPayTech(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'technician';
    $u->save();

    return $u;
}

// ---------------------------------------------------------------- webhook ---

it('finalizes an order via webhook when the Mollie amount matches', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $order = auditPayOrder(121.00);
    AuditFakeMollie::$next = auditFakePayment(121.00, ['order_id' => $order->id]);

    $this->postJson('/payment/webhook', ['id' => 'tr_audit_order'])
        ->assertOk()
        ->assertJson(['status' => 'ok']);

    expect(Order::find($order->id)->payment_status)->toBe('paid');
    expect(OrderInvoice::where('order_id', $order->id)->count())->toBe(1);
});

it('refuses to finalize when the Mollie amount mismatches the order total', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $order = auditPayOrder(121.00);
    // Attacker paid €1 for a €121 order (or total changed after creation).
    AuditFakeMollie::$next = auditFakePayment(1.00, ['order_id' => $order->id]);

    $this->postJson('/payment/webhook', ['id' => 'tr_audit_order'])
        ->assertOk()
        ->assertJson(['status' => 'amount-mismatch']);

    expect(Order::find($order->id)->payment_status)->not->toBe('paid');
    expect(OrderInvoice::where('order_id', $order->id)->count())->toBe(0);
});

it('treats a repeated webhook as a no-op (single invoice)', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $order = auditPayOrder(50.00);
    AuditFakeMollie::$next = auditFakePayment(50.00, ['order_id' => $order->id]);

    $this->postJson('/payment/webhook', ['id' => 'tr_audit_order'])->assertOk();
    $this->postJson('/payment/webhook', ['id' => 'tr_audit_order'])->assertOk();

    expect(OrderInvoice::where('order_id', $order->id)->count())->toBe(1);
});

// ------------------------------------------------------------- return views ---

it('hides order details on the return page from strangers', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $owner = User::factory()->create();
    $order = auditPayOrder(30.00, $owner);
    $order->update(['payment_status' => 'paid']);

    // Stranger (logged out, no session binding): generic page, no PII.
    $this->get('/payment/return/'.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number)
        ->assertDontSee('klant@voorbeeld.nl');
});

it('shows order details on the return page to the session-bound guest buyer', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $order = auditPayOrder(30.00); // guest order (user_id null)
    $order->update(['payment_status' => 'paid']);

    $this->withSession(['owned_orders' => [$order->id]])
        ->get('/payment/return/'.$order->id)
        ->assertOk()
        ->assertSee($order->order_number);
});

it('never celebrates an unpaid order on the generic success page', function () {
    $order = auditPayOrder(30.00);

    $this->withSession(['owned_orders' => [$order->id]])
        ->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number);
});

// --------------------------------------------------------------- technician ---

it('blocks guests and customers from the technician payment page', function () {
    $client = User::factory()->create(['klantnummer' => 'SLP-777001']);

    // Guest: sent to login.
    $this->get('/technician/payment/SLP-777001')->assertRedirect('/login');

    // Plain customer: bounced to the technician login (page exposes client PII).
    $this->actingAs(User::factory()->create())
        ->get('/technician/payment/SLP-777001')
        ->assertRedirect(route('technician.login'));

    // Technician operator: allowed.
    $this->actingAs(auditPayTech())
        ->get('/technician/payment/SLP-777001')
        ->assertOk()
        ->assertSee('SLP-777001');
});

it('does not burn a coupon until the technician payment succeeds', function () {
    Mail::fake();
    auditPayMollie(); // fake gateway throws on createPayment: no real Mollie
    $tech = auditPayTech();
    $client = User::factory()->create(['klantnummer' => 'SLP-777002', 'email' => 'client@voorbeeld.nl']);
    $coupon = Coupon::create([
        'code' => 'AUDIT10', 'discount_type' => 'fixed', 'discount_value' => 5,
        'status' => true, 'usage_limit' => 10, 'used_count' => 0, 'is_single_use' => true,
    ]);
    TechnicianSetting::updateOrCreate(['key' => 'hour_price'], ['key' => 'hour_price', 'value' => '60']);
    TechnicianSetting::updateOrCreate(['key' => 'travel_cost'], ['key' => 'travel_cost', 'value' => '0']);

    $this->actingAs($tech)->postJson('/technician/payment/submit', [
        'klantnummer' => 'SLP-777002',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'coupon_code' => 'AUDIT10',
    ])->assertStatus(502); // no Mollie configured in test env

    $form = TechnicianForm::latest('id')->first();
    expect($form->coupon_id)->toBe($coupon->id);
    // Coupon must NOT be consumed while unpaid.
    expect(CouponUsage::where('coupon_id', $coupon->id)->count())->toBe(0);
    expect(Coupon::find($coupon->id)->used_count)->toBe(0);
});

it('rejects technician coupons below their minimum amount', function () {
    $tech = auditPayTech();
    $client = User::factory()->create(['klantnummer' => 'SLP-777003']);
    Coupon::create([
        'code' => 'BIGSPEND', 'discount_type' => 'fixed', 'discount_value' => 5,
        'min_amount' => 500, 'status' => true, 'is_single_use' => false,
    ]);
    TechnicianSetting::updateOrCreate(['key' => 'hour_price'], ['key' => 'hour_price', 'value' => '60']);
    TechnicianSetting::updateOrCreate(['key' => 'travel_cost'], ['key' => 'travel_cost', 'value' => '0']);

    $this->actingAs($tech)->postJson('/technician/check-coupon', [
        'klantnummer' => 'SLP-777003',
        'start_time' => '09:00',
        'end_time' => '10:00',
        'coupon_code' => 'BIGSPEND',
    ])->assertStatus(422);
});

it('consumes the coupon on technician finalize and enforces the amount', function () {
    Storage::fake('local');
    Mail::fake();
    auditPayMollie();
    $tech = auditPayTech();
    $client = User::factory()->create(['klantnummer' => 'SLP-777004', 'email' => 'c4@voorbeeld.nl']);
    $coupon = Coupon::create([
        'code' => 'AUDIT5', 'discount_type' => 'fixed', 'discount_value' => 5,
        'status' => true, 'usage_limit' => 10, 'used_count' => 0, 'is_single_use' => true,
    ]);
    TechnicianSetting::updateOrCreate(['key' => 'hour_price'], ['key' => 'hour_price', 'value' => '60']);
    TechnicianSetting::updateOrCreate(['key' => 'travel_cost'], ['key' => 'travel_cost', 'value' => '0']);

    $form = TechnicianForm::create([
        'user_id' => $client->id, 'technician_id' => $tech->id,
        'start_time' => '09:00', 'end_time' => '10:00',
        'duration_minutes' => 60, 'quarter_count' => 4, 'quarter_price' => 15,
        'travel_cost' => 0, 'subtotal' => 50, 'btw' => 10.50, 'total' => 60.50,
        'coupon_id' => $coupon->id, 'coupon_discount' => 5,
        'payment_status' => 'unpaid', 'mollie_payment_id' => 'tr_audit_tech',
    ]);
    $form->technicianInvoice()->create([
        'invoice_date' => now()->format('Y-m-d'), 'subtotal' => 50,
        'btw' => 10.50, 'total' => 60.50, 'status' => 'unpaid',
    ]);

    AuditFakeMollie::$next = auditFakePayment(60.50, ['technician_form_id' => $form->id]);
    $this->postJson('/technician/webhook', ['id' => 'tr_audit_tech'])
        ->assertOk()->assertJson(['status' => 'ok']);

    expect(TechnicianForm::find($form->id)->payment_status)->toBe('paid');
    expect(CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $client->id)->count())->toBe(1);
    expect((int) Coupon::find($coupon->id)->used_count)->toBe(1);

    // Underpaid technician form must not finalize.
    $form2 = TechnicianForm::create([
        'user_id' => $client->id, 'technician_id' => $tech->id,
        'start_time' => '09:00', 'end_time' => '10:00',
        'duration_minutes' => 60, 'quarter_count' => 4, 'quarter_price' => 15,
        'travel_cost' => 0, 'subtotal' => 50, 'btw' => 10.50, 'total' => 60.50,
        'payment_status' => 'unpaid', 'mollie_payment_id' => 'tr_audit_tech2',
    ]);
    AuditFakeMollie::$next = auditFakePayment(1.00, ['technician_form_id' => $form2->id]);
    $this->postJson('/technician/webhook', ['id' => 'tr_audit_tech2'])
        ->assertOk()->assertJson(['status' => 'amount-mismatch']);
    expect(TechnicianForm::find($form2->id)->payment_status)->not->toBe('paid');
});

// --------------------------------------------------------------- membership ---

it('creates unique membership invoice numbers and takes the price server-side', function () {
    Mail::fake();
    MembershipSetting::create(['subscription_price' => 25]);
    config(['services.mollie.key' => '']);

    $payload = [
        'customer_type' => 'particulier', 'customer_gender' => 'man',
        'name' => 'Lid Een', 'customer_email' => 'lid1@voorbeeld.nl',
        'customer_phone' => '0612345678', 'customer_address' => 'Straat 1',
        'postcode' => '1234AB', 'city' => 'Apeldoorn',
        'terms' => true,
    ];

    $this->postJson('/lid-worden', $payload)->assertStatus(502);
    $this->postJson('/lid-worden', array_merge($payload, ['customer_email' => 'lid2@voorbeeld.nl']))->assertStatus(502);

    $numbers = MembershipInvoice::pluck('invoice_number');
    expect($numbers->count())->toBe(2);
    expect($numbers->unique()->count())->toBe(2);
    expect($numbers->first())->toStartWith('LID-');
    expect((float) Membership::latest('id')->first()->total)->toBe(25.0);
});

it('hides membership success pages from strangers', function () {
    $m = Membership::create([
        'klantnummer' => 'SMP-TEST123456', 'customer_type' => 'private',
        'customer_gender' => 'man', 'name' => 'Lid X',
        'customer_email' => 'lidx@voorbeeld.nl', 'customer_phone' => '0612345678',
        'customer_address' => 'Straat 1', 'postcode' => '1234AB', 'city' => 'Apeldoorn',
        'start_date' => now(), 'end_date' => now()->addYear(),
        'total' => 25, 'payment_status' => 'paid', 'payment_method' => 'mollie',
        'terms_accepted' => true,
    ]);

    $this->get('/lid-worden/success/'.$m->id)->assertNotFound();

    $this->withSession(['lidmaatschap_'.$m->id => true])
        ->get('/lid-worden/success/'.$m->id)
        ->assertOk();
});

// ------------------------------------------------------------------ licenses ---

it('refuses to delete a sold license code', function () {
    $admin = auditPayAdmin();
    $category = Category::create(['name' => 'Software', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Audit Soft',
        'price' => 10, 'stock_status' => 'in_stock', 'status' => true, 'is_digital' => true,
    ]);
    $sold = LicenseCode::create(['product_id' => $product->id, 'code' => 'SOLD-1', 'status' => 'sold', 'order_id' => 999]);
    $free = LicenseCode::create(['product_id' => $product->id, 'code' => 'FREE-1', 'status' => 'available']);

    $this->actingAs($admin)->deleteJson('/admin/webshop/license-codes/'.$sold->id)->assertStatus(422);
    expect(LicenseCode::find($sold->id))->not->toBeNull();

    $this->actingAs($admin)->deleteJson('/admin/webshop/license-codes/'.$free->id)->assertOk();
    expect(LicenseCode::find($free->id))->toBeNull();
});

// ----------------------------------------------------------------- downloads ---

it('issues expiring signed download links', function () {
    Storage::fake('local');
    $file = DigitalFile::create(['name' => 'setup.zip', 'path' => 'digital/abc.zip', 'size' => 10, 'mime' => 'application/zip']);
    $order = auditPayOrder(10.00);

    $url = $file->signedUrlForOrder($order);

    expect($url)->toContain('expires=');
    expect($url)->toContain('signature=');
});

// ------------------------------------------------------------------- ai + misc ---

it('keeps the AI endpoint admin-only and caps feature input', function () {
    // Guest: login wall.
    $this->postJson('/admin/webshop/products/generate-description', ['title' => 'X'])
        ->assertUnauthorized();

    // Admin with 21 features: rejected (cap is 20).
    $this->actingAs(auditPayAdmin())->postJson('/admin/webshop/products/generate-description', [
        'title' => 'Laptop',
        'features' => array_fill(0, 21, 'snelle processor'),
    ])->assertStatus(422)->assertJsonValidationErrors(['features']);
});

it('rejects afspraak honeypot fills', function () {
    $this->postJson('/afspraak/submit', [
        'name' => 'Bot', 'email' => 'bot@voorbeeld.nl', 'street' => 'Straat',
        'phone' => '0612345678', 'postcode' => '1234AB', 'house_number' => '1',
        'city' => 'Apeldoorn', 'device' => 'Laptop', 'problem' => 'Gaat niet aan.',
        'preferred_date' => now()->addDay()->format('Y-m-d'),
        'preferred_time' => '09:00 - 11:00',
        'website' => 'http://spam.example',
    ])->assertStatus(422);
});
