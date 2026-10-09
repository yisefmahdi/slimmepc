<?php

/*
 * Sub-agent F (testing audit): IDOR / object-level authorization tests.
 * User A must never read, download or mutate user B's data.
 */

use App\Models\Address;
use App\Models\Cart;
use App\Models\Category;
use App\Models\DigitalFile;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\OrderInvoice;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

function auditIdorAdmin(): User
{
    $u = User::factory()->create();
    $u->role = 'admin';
    $u->save();

    return $u;
}

function auditIdorOrder(User $user, array $overrides = []): Order
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

function auditIdorInvoice(Order $order, string $number): OrderInvoice
{
    Storage::disk('local')->put('invoices/orders/'.$number.'.pdf', 'fake-pdf');

    return OrderInvoice::create([
        'order_id' => $order->id,
        'invoice_number' => $number,
        'invoice_date' => now()->toDateString(),
        'customer_name' => 'Jan Jansen',
        'customer_email' => $order->customer_email,
        'subtotal' => 100,
        'tax_percentage' => 21,
        'tax_amount' => 17.36,
        'total' => 106.95,
        'pdf_path' => 'invoices/orders/'.$number.'.pdf',
    ]);
}

it('blocks guests and strangers from order details and invoices', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $order = auditIdorOrder($owner);
    auditIdorInvoice($order, 'INV-2026-IDOR1');

    $this->get('/mijn-bestellingen/'.$order->order_number)->assertRedirect('/login');
    $this->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertRedirect('/login');

    $this->actingAs($stranger)->get('/mijn-bestellingen/'.$order->order_number)->assertNotFound();
    $this->actingAs($stranger)->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertNotFound();

    $this->actingAs($owner)->get('/mijn-bestellingen/'.$order->order_number)->assertOk();
    $this->actingAs($owner)->get('/mijn-bestellingen/'.$order->order_number.'/factuur')->assertOk();
});

it('never lists another user order in the history', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $orderB = auditIdorOrder($b);

    $this->actingAs($a)->get('/mijn-bestellingen')
        ->assertOk()
        ->assertDontSee($orderB->order_number);
});

it('blocks deleting another user favourite', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();
    $category = Category::create(['name' => 'Audio', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Koptelefoon X', 'price' => 50,
        'stock_status' => 'in_stock', 'status' => true, 'description' => 'test',
    ]);
    $fav = Favorite::create(['user_id' => $a->id, 'product_id' => $product->id]);

    $this->delete('/wishlist/'.$fav->id)->assertRedirect('/login');
    $this->actingAs($b)->deleteJson('/wishlist/'.$fav->id)->assertForbidden();
    $this->assertDatabaseHas('favorites', ['id' => $fav->id]);

    $this->actingAs($a)->deleteJson('/wishlist/'.$fav->id)->assertOk();
    $this->assertDatabaseMissing('favorites', ['id' => $fav->id]);
});

it('ignores a stranger saved address id at checkout', function () {
    $victim = User::factory()->create();
    $attacker = User::factory()->create();
    $victimAddress = Address::create([
        'user_id' => $victim->id, 'first_name' => 'Victim', 'last_name' => 'User',
        'street' => 'Straat', 'house_number' => '1', 'postcode' => '1234AB',
        'city' => 'Apeldoorn', 'country' => 'Nederland', 'phone' => '0612345678',
        'email' => $victim->email, 'type' => 'billing',
    ]);

    $category = Category::create(['name' => 'Laptops', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Laptop A', 'price' => 24.00,
        'stock_status' => 'in_stock', 'status' => true, 'description' => 'test',
    ]);
    $cart = Cart::create(['user_id' => $attacker->id]);
    app(CartService::class)->addItem($cart, $product, 1);
    config(['services.mollie.key' => '']);

    $this->actingAs($attacker)->postJson('/checkout', [
        'email' => $attacker->email, 'first_name' => 'Attacker', 'last_name' => 'User',
        'street' => 'Eigen straat', 'house_number' => '9', 'postcode' => '1234 AB',
        'city' => 'Apeldoorn', 'country' => 'Nederland', 'phone' => '0687654321',
        'shipping_method' => 'delivery', 'terms' => true,
        'saved_address_id' => $victimAddress->id,
    ])->assertStatus(201);

    $order = Order::latest('id')->first();
    expect((int) $order->billing_address_id)->not->toBe((int) $victimAddress->id);
    expect((int) $order->user_id)->toBe((int) $attacker->id);
});

it('rejects unsigned, tampered and expired download links', function () {
    Storage::disk('local')->put('digital/idor-test.zip', 'fake-binary-content');
    $file = DigitalFile::create([
        'name' => 'idor-test.zip', 'path' => 'digital/idor-test.zip', 'size' => 18, 'mime' => 'application/zip',
    ]);
    $category = Category::create(['name' => 'Software', 'status' => true, 'sort_order' => 0]);
    $product = Product::create([
        'category_id' => $category->id, 'title' => 'Office Pakket', 'price' => 60,
        'stock_status' => 'in_stock', 'status' => true, 'is_digital' => true,
        'download_64bit_url' => $file->routeUrl(), 'description' => 'test',
    ]);
    $owner = User::factory()->create();
    $order = Order::create([
        'user_id' => $owner->id, 'customer_email' => $owner->email, 'customer_phone' => '0612345678',
        'subtotal' => 60, 'total_price' => 60, 'payment_status' => 'paid', 'order_status' => 'processing',
    ]);
    $order->items()->create([
        'product_id' => $product->id, 'product_name' => $product->title,
        'product_price' => 60, 'quantity' => 1, 'total_price' => 60,
    ]);

    // Plain order query without signature: 403.
    $this->get('/download/bestand/'.$file->id.'?order='.$order->order_number)->assertForbidden();

    // Tampered order number on an otherwise valid signature: 403.
    $signed = $file->signedUrlForOrder($order);
    $tampered = preg_replace('/order=[^&]+/', 'order=ORD-TAMPERED', $signed);
    $this->get($tampered)->assertForbidden();

    // Expired signature: 403.
    $expired = URL::temporarySignedRoute(
        'download.file', now()->subMinute(), ['file' => $file->id, 'order' => $order->order_number]
    );
    $this->get($expired)->assertForbidden();

    // Revoked access (order no longer paid): 403 even with a fresh signature.
    $order->update(['payment_status' => 'failed']);
    $this->get($file->signedUrlForOrder($order->fresh()))->assertForbidden();

    // Stranger with no order at all: 403.
    $this->actingAs(User::factory()->create())->get($file->routeUrl())->assertForbidden();
});

it('hides paid order details on payment pages from strangers', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $order = auditIdorOrder($owner);

    $this->actingAs($stranger)->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number);

    $this->actingAs($owner)->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertSee($order->order_number);

    // Unpaid orders are never celebrated, not even for the owner.
    $order->update(['payment_status' => 'pending']);
    $this->actingAs($owner)->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number);
});

it('ignores role escalation on profile update', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', [
        'name' => 'Normale Naam',
        'email' => $user->email,
        'role' => 'admin',
        'is_blocked' => true,
    ])->assertRedirect();

    expect($user->fresh()->role)->toBe('user')
        ->and((bool) $user->fresh()->is_blocked)->toBeFalse();
});

it('lets only admins change user roles', function () {
    $target = User::factory()->create();
    $tech = User::factory()->create(['role' => 'technician']);
    $plain = User::factory()->create();

    $this->actingAs($plain)->postJson('/admin/users/'.$target->id.'/role', ['role' => 'admin'])->assertForbidden();
    $this->actingAs($tech)->postJson('/admin/users/'.$target->id.'/role', ['role' => 'admin'])->assertForbidden();
    expect($target->fresh()->role)->toBe('user');

    $this->actingAs(auditIdorAdmin())->postJson('/admin/users/'.$target->id.'/role', ['role' => 'technician'])
        ->assertOk()
        ->assertJsonPath('role', 'technician');
    expect($target->fresh()->role)->toBe('technician');

    $this->actingAs(auditIdorAdmin())->postJson('/admin/users/'.$target->id.'/role', ['role' => 'superadmin'])
        ->assertStatus(422);
});

it('blocks non-admins from admin order and invoice endpoints', function () {
    $owner = User::factory()->create();
    $order = auditIdorOrder($owner);
    $tech = User::factory()->create(['role' => 'technician']);

    foreach ([$owner, $tech] as $actor) {
        $this->actingAs($actor)->get('/admin/orders')->assertForbidden();
        $this->actingAs($actor)->get('/admin/orders/'.$order->id)->assertForbidden();
        $this->actingAs($actor)->postJson('/admin/orders/'.$order->id.'/status', ['order_status' => 'shipped'])->assertForbidden();
        $this->actingAs($actor)->get('/admin/orders/'.$order->id.'/invoice')->assertForbidden();
    }
    Auth::logout();
    $this->get('/admin/orders')->assertRedirect('/login');
});
