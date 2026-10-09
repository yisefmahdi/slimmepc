<?php

/*
 * Sub-agent B (Authentication & Authorization) regression tests.
 * Audit report: audit/02-auth.md ([AUTH-01] … [AUTH-10]).
 *
 * SQLite :memory: via phpunit.xml — never touches MySQL slimmepc_2026.
 */

use App\Models\Membership;
use App\Models\Order;
use App\Models\TechnicianForm;
use App\Models\User;

function makeAuthUser(string $role = 'user', array $overrides = []): User
{
    $user = User::factory()->create($overrides);
    if ($user->role !== $role) {
        $user->role = $role;
        $user->save();
    }

    return $user->fresh();
}

function makeAuthTechAndClient(): array
{
    $tech = User::factory()->create();
    $tech->role = 'technician';
    $tech->save();

    $client = User::factory()->create(); // klantnummer via model boot (SLP-######)

    return [$tech->fresh(), $client->fresh()];
}

// [AUTH-01] Mass assignment: role/is_blocked/klantnummer/email_verified_at
// are NOT fillable anymore.
it('ignores privileged fields on mass assignment', function () {
    $user = User::create([
        'name' => 'Sneaky',
        'email' => 'sneaky@example.com',
        'password' => 'password',
        'role' => 'admin',
        'is_blocked' => true,
        'klantnummer' => 'ADMIN-0001',
        'email_verified_at' => now(),
    ]);

    // NOTE: fresh() re-reads the row — DB column defaults ('user'/false)
    // apply on INSERT; the in-memory instance keeps null for ignored keys.
    $fresh = $user->fresh();
    expect($fresh->role)->toBe('user')
        ->and((bool) $fresh->is_blocked)->toBeFalse()
        ->and($fresh->klantnummer)->not->toBe('ADMIN-0001');
});

it('does not escalate role via profile update', function () {
    $user = makeAuthUser('user');

    $this->actingAs($user)->patch('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'admin',
        'is_blocked' => true,
    ])->assertRedirect('/profile');

    $fresh = $user->fresh();
    expect($fresh->role)->toBe('user')
        ->and((bool) $fresh->is_blocked)->toBeFalse();
});

it('does not escalate role via registration', function () {
    $this->post('/register', [
        'name' => 'Sneaky',
        'email' => 'sneaky2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
    ]);

    $this->assertAuthenticated();
    expect(auth()->user()->fresh()->role)->toBe('user');
});

it('still lets admins manage roles through the admin controller', function () {
    $admin = makeAuthUser('admin');
    $target = makeAuthUser('user');

    // Store with explicit role.
    $this->actingAs($admin)->postJson('/admin/users', [
        'name' => 'New Tech',
        'email' => 'newtech@example.com',
        'password' => 'secret123',
        'role' => 'technician',
    ])->assertCreated();

    expect(User::where('email', 'newtech@example.com')->first()->role)->toBe('technician');

    // Role change + block toggle keep working via explicit assignment.
    $this->actingAs($admin)
        ->postJson("/admin/users/{$target->id}/role", ['role' => 'technician'])
        ->assertOk();
    expect($target->fresh()->role)->toBe('technician');

    $this->actingAs($admin)
        ->postJson("/admin/users/{$target->id}/toggle-block", [])
        ->assertOk();
    expect((bool) $target->fresh()->is_blocked)->toBeTrue();
});

// [AUTH-02] Technician payment flow requires an authenticated operator.
// Route layer (`auth` middleware) sends guests to /login; the controller
// role gate (requireTechnician) then sends non-technicians to /betaal/login.
it('redirects guests away from the technician payment page', function () {
    $client = User::factory()->create();

    $this->get('/technician/payment/'.$client->klantnummer)
        ->assertRedirect('/login');
});

it('blocks guests from creating technician payment forms', function () {
    $client = User::factory()->create();

    $this->post('/technician/payment/submit', [
        'klantnummer' => $client->klantnummer,
        'start_time' => '09:00',
        'end_time' => '10:00',
    ])->assertRedirect('/login');

    expect(TechnicianForm::count())->toBe(0);
});

it('blocks guests from the technician quote and coupon endpoints', function () {
    $client = User::factory()->create();

    $this->postJson('/technician/quote', [
        'klantnummer' => $client->klantnummer,
        'start_time' => '09:00',
        'end_time' => '10:00',
    ])->assertUnauthorized();

    $this->postJson('/technician/check-coupon', [
        'klantnummer' => $client->klantnummer,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'coupon_code' => 'X',
    ])->assertUnauthorized();
});

it('blocks regular users from the technician payment page', function () {
    $user = makeAuthUser('user');
    $client = User::factory()->create();

    $this->actingAs($user)
        ->get('/technician/payment/'.$client->klantnummer)
        ->assertRedirect(route('technician.login'));
});

it('lets a logged-in technician open the payment page', function () {
    [$tech, $client] = makeAuthTechAndClient();

    $this->actingAs($tech)
        ->get('/technician/payment/'.$client->klantnummer)
        ->assertOk();
});

// [AUTH-08] Technician login regenerates the session (fixation).
it('regenerates the session on technician login', function () {
    [$tech, $client] = makeAuthTechAndClient();

    $oldId = session()->getId();

    $this->post('/betaal/login', [
        'email' => $tech->email,
        'password' => 'password',
        'klantnummer' => $client->klantnummer,
    ])->assertRedirect(route('technician.payment', ['klantnummer' => $client->klantnummer]));

    expect(session()->getId())->not->toBe($oldId);
    $this->assertAuthenticatedAs($tech);
});

// [AUTH-09] Technician login reveals nothing about which field was wrong.
it('returns a generic error for every technician login failure', function () {
    [$tech, $client] = makeAuthTechAndClient();

    // Wrong password.
    $this->post('/betaal/login', [
        'email' => $tech->email,
        'password' => 'wrong-password',
        'klantnummer' => $client->klantnummer,
    ])->assertSessionHasErrors('email');
    expect(session('errors')->get('email')[0])->toBe('Verkeerde inloggegevens.');
    $this->assertGuest();

    // Correct credentials but unknown klantnummer: same generic message.
    $this->post('/betaal/login', [
        'email' => $tech->email,
        'password' => 'password',
        'klantnummer' => 'SLP-000000',
    ]);
    $messages = session('errors')->all();
    expect($messages)->toHaveCount(1)->and($messages[0])->toBe('Verkeerde inloggegevens.');
    $this->assertGuest();

    // Non-technician account: same generic message.
    $user = makeAuthUser('user');
    $this->post('/betaal/login', [
        'email' => $user->email,
        'password' => 'password',
        'klantnummer' => $client->klantnummer,
    ]);
    expect(session('errors')->all()[0])->toBe('Verkeerde inloggegevens.');
    $this->assertGuest();
});

it('refuses login for blocked technicians without revealing the block', function () {
    [$tech, $client] = makeAuthTechAndClient();
    $tech->is_blocked = true;
    $tech->save();

    $this->post('/betaal/login', [
        'email' => $tech->email,
        'password' => 'password',
        'klantnummer' => $client->klantnummer,
    ]);

    expect(session('errors')->all()[0])->toBe('Verkeerde inloggegevens.');
    $this->assertGuest();
});

// Blocked users lose usable sessions on every web request.
it('logs out blocked users on the next web request', function () {
    $user = makeAuthUser('user');
    $user->is_blocked = true;
    $user->save();

    $this->actingAs($user->fresh())
        ->get('/profile')
        ->assertRedirect('/login');

    $this->assertGuest();
});

// Admin boundary: guests/users/techs on /admin/*.
it('enforces the admin boundary', function () {
    // Guests are sent to login.
    $this->get('/admin')->assertRedirect('/login');
    $this->get('/admin/users')->assertRedirect('/login');

    // Regular users get 403 everywhere under /admin.
    $user = makeAuthUser('user');
    $this->actingAs($user)->get('/admin')->assertForbidden();
    $this->actingAs($user)->get('/admin/users')->assertForbidden();

    // Technicians may open the dashboard shell but not admin-only pages.
    $tech = makeAuthUser('technician');
    $this->actingAs($tech)->get('/admin')->assertOk();
    $this->actingAs($tech)->get('/admin/users')->assertForbidden();
    $this->actingAs($tech)->get('/admin/orders')->assertForbidden();

    // Admins pass.
    $admin = makeAuthUser('admin');
    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs($admin)->get('/admin/users')->assertOk();
});

it('lets technicians manage role changes only via admin routes they cannot reach', function () {
    $tech = makeAuthUser('technician');
    $target = makeAuthUser('user');

    $this->actingAs($tech)
        ->postJson("/admin/users/{$target->id}/role", ['role' => 'admin'])
        ->assertForbidden();

    expect($target->fresh()->role)->toBe('user');
});

// [AUTH-06] verified middleware is a documented no-op (documents real effect:
// unverified admins are NOT blocked — enable MustVerifyEmail to change).
it('does not block unverified users via the verified middleware', function () {
    $admin = User::factory()->unverified()->create();
    $admin->role = 'admin';
    $admin->save();

    expect($admin->fresh()->hasVerifiedEmail())->toBeFalse();

    $this->actingAs($admin->fresh())->get('/admin')->assertOk();
});

// [AUTH-04] Public order result pages hide other people's orders.
it('hides other peoples orders on the public payment success page', function () {
    $owner = makeAuthUser('user');
    $stranger = makeAuthUser('user');
    $order = Order::create([
        'user_id' => $owner->id,
        'customer_email' => $owner->email,
        'customer_phone' => '0612345678',
        'subtotal' => 100,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);

    // Stranger (and guests) get the generic page without details.
    $this->actingAs($stranger)
        ->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number)
        ->assertDontSee($owner->email);

    $this->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertDontSee($order->order_number);

    // The owner still sees their confirmation.
    $this->actingAs($owner)
        ->get('/payment/success?order='.$order->id)
        ->assertOk()
        ->assertSee($order->order_number);
});

it('hides other peoples orders on the payment return page', function () {
    $owner = makeAuthUser('user');
    $stranger = makeAuthUser('user');
    $order = Order::create([
        'user_id' => $owner->id,
        'customer_email' => $owner->email,
        'customer_phone' => '0612345678',
        'subtotal' => 100,
        'total_price' => 106.95,
        'payment_status' => 'paid',
        'order_status' => 'processing',
    ]);

    $this->actingAs($stranger)
        ->get('/payment/return/'.$order->id)
        ->assertOk()
        ->assertDontSee($owner->email);
});

// Membership success page is bound to payer session / owner / admin.
it('blocks strangers from other peoples membership success pages', function () {
    $owner = makeAuthUser('user');
    $stranger = makeAuthUser('user');
    $membership = Membership::create([
        'user_id' => $owner->id,
        'klantnummer' => 'SMP-TEST-000001',
        'name' => 'Lid Example',
        'customer_email' => 'lid@example.com',
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'total' => 50,
        'payment_status' => 'paid',
    ]);

    $this->actingAs($stranger)
        ->get('/lid-worden/success/'.$membership->id)
        ->assertNotFound();

    $this->get('/lid-worden/success/'.$membership->id)
        ->assertNotFound();

    $this->actingAs($owner)
        ->get('/lid-worden/success/'.$membership->id)
        ->assertOk()
        ->assertSee('Lid Example');
});

// Technician success page is bound to payer session / operator / admin.
it('blocks strangers from other peoples technician success pages', function () {
    [$tech, $client] = makeAuthTechAndClient();
    $form = TechnicianForm::create([
        'user_id' => $client->id,
        'technician_id' => $tech->id,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'duration_minutes' => 60,
        'quarter_count' => 4,
        'quarter_price' => 10,
        'travel_cost' => 5,
        'subtotal' => 37.19,
        'btw' => 7.81,
        'total' => 45,
        'payment_status' => 'paid',
    ]);

    $stranger = makeAuthUser('user');

    $this->actingAs($stranger)
        ->get('/technician/success/'.$form->id)
        ->assertNotFound();

    $this->get('/technician/success/'.$form->id)
        ->assertNotFound();
});

// [AUTH-07] Registration endpoint is throttled against mass account creation.
it('throttles mass registration attempts', function () {
    for ($i = 0; $i < 10; $i++) {
        $this->post('/register', [
            'name' => 'User '.$i,
            'email' => "throttle{$i}@example.com",
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        // Each attempt authenticates a new user; log out for the next round.
        auth()->logout();
    }

    $this->post('/register', [
        'name' => 'Blocked',
        'email' => 'throttled@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertStatus(429);
});
