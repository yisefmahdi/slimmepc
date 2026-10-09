<?php

/*
 * Sub-agent F (testing audit): route smoke + authorization matrix + core auth behaviours.
 * Covers every named GET route family for guest/user/technician/admin.
 */

use App\Models\User;
use Illuminate\Support\Facades\Auth;

function auditMatrixAdmin(): User
{
    $u = User::factory()->create();
    $u->role = 'admin';
    $u->save();

    return $u;
}

function auditMatrixTech(): User
{
    $u = User::factory()->create();
    $u->role = 'technician';
    $u->save();

    return $u;
}

/** Admin-only GET pages: guests are sent to login, users AND technicians get 403, admins get 200 (or 302 for redirects). */
function auditAdminOnlyGetUrls(): array
{
    return [
        '/admin/dashboard',
        '/admin/content/design',
        '/admin/content/home/section/hero',
        '/admin/users',
        '/admin/users/data',
        '/admin/contact-inbox',
        '/admin/reparatie-aanmeldingen',
        '/admin/afspraak-aanvragen',
        '/admin/bevestiging-mail/hardware',
        '/admin/leen-huur',
        '/admin/lidmaatschap',
        '/admin/monteur',
        '/admin/chat/inbox',
        '/admin/chat/faqs',
        '/admin/chat/beschikbaarheid',
        '/admin/webshop/categories',
        '/admin/webshop/products',
        '/admin/webshop/bestanden',
        '/admin/webshop/license-codes',
        '/admin/webshop/coupons',
        '/admin/webshop/reviews/data',
        '/admin/orders',
        '/admin/shipping',
        '/admin/notificaties',
        '/admin/email-verzenden',
        '/admin/mailinglijst',
        '/admin/mailinglijst/verzenden',
        '/admin/purchase-sales',
        '/admin/rekenmachine',
    ];
}

/** Admin-shell pages technicians may legitimately open (dashboard + device receipts). */
function auditTechAllowedGetUrls(): array
{
    return [
        '/admin',
        '/admin/dashboard',
        '/admin/bevestiging-mail/ontvangst',
        '/admin/bevestiging-mail/ontvangst/data',
        '/admin/bevestiging-mail/ontvangst/create',
    ];
}

it('redirects guests to login on every admin-only page', function () {
    foreach (auditAdminOnlyGetUrls() as $url) {
        $this->get($url)->assertRedirect('/login');
    }
    foreach (auditTechAllowedGetUrls() as $url) {
        $this->get($url)->assertRedirect('/login');
    }
});

it('returns 403 for regular users on every admin page', function () {
    $user = User::factory()->create();

    foreach (array_merge(auditAdminOnlyGetUrls(), auditTechAllowedGetUrls()) as $url) {
        $this->actingAs($user)->get($url)->assertForbidden();
    }
});

it('returns 403 for technicians on admin-only pages but allows the dashboard and device receipts', function () {
    $tech = auditMatrixTech();

    foreach (auditAdminOnlyGetUrls() as $url) {
        // Dashboard is in both lists; technicians may open it.
        if ($url === '/admin/dashboard') {
            $this->actingAs($tech)->get($url)->assertOk();

            continue;
        }
        $this->actingAs($tech)->get($url)->assertForbidden();
    }

    foreach (auditTechAllowedGetUrls() as $url) {
        $this->actingAs($tech)->get($url)->assertOk();
    }
});

it('renders every admin-only page for admins', function () {
    $admin = auditMatrixAdmin();

    foreach (auditAdminOnlyGetUrls() as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }
    foreach (auditTechAllowedGetUrls() as $url) {
        $this->actingAs($admin)->get($url)->assertOk();
    }

    // Redirect-style admin routes.
    $this->actingAs($admin)->get('/admin/content')->assertRedirect(route('admin.content.design.edit'));
    $this->actingAs($admin)->get('/admin')->assertOk();
});

it('smoke-tests public GET routes for guests', function ($url, $status) {
    $this->get($url)->assertStatus($status);
})->with([
    'home' => ['/', 200],
    'tarieven' => ['/tarieven', 200],
    'contact' => ['/contact', 200],
    'over-ons' => ['/over-ons', 200],
    'privacy' => ['/privacy', 200],
    'voorwaarden' => ['/voorwaarden', 200],
    'reparatie' => ['/reparatie-aanmelden', 200],
    'afspraak' => ['/afspraak', 200],
    'zoeken leeg' => ['/zoeken', 200],
    'zoeken query' => ['/zoeken?q=laptop', 200],
    'cart' => ['/cart', 200],
    'cart count' => ['/cart/count', 200],
    'checkout leeg redirect' => ['/checkout', 302],
    'lid-worden' => ['/lid-worden', 200],
    'betaal login' => ['/betaal/login', 200],
    'login' => ['/login', 200],
    'register' => ['/register', 200],
    'forgot-password' => ['/forgot-password', 200],
    'sitemap' => ['/sitemap.xml', 200],
    'track' => ['/track', 200],
    'firebase sw' => ['/firebase-messaging-sw.js', 200],
    'ai-chat status' => ['/ai-chat/status', 200],
    'payment success' => ['/payment/success', 200],
    'payment failed' => ['/payment/failed', 200],
    'onbekende dienst 404' => ['/diensten/bestaat-niet', 404],
]);

it('redirects guests to login for account pages', function () {
    $this->get('/wishlist')->assertRedirect('/login');
    $this->get('/mijn-bestellingen')->assertRedirect('/login');
    $this->get('/profile')->assertRedirect('/login');
});

it('renders account pages for logged-in users', function () {
    $user = User::factory()->create(['name' => 'Audit Gebruiker']);

    $this->actingAs($user)->get('/wishlist')->assertOk();
    $this->actingAs($user)->get('/mijn-bestellingen')->assertOk();
    $this->actingAs($user)->get('/profile')->assertOk();
    // Personalized header proves the user is recognized.
    $this->actingAs($user)->get('/')->assertOk()->assertSee('Audit Gebruiker');
});

it('redirects logins by role', function () {
    $admin = auditMatrixAdmin();
    $user = User::factory()->create();
    $tech = auditMatrixTech();

    $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
        ->assertRedirect(route('admin.dashboard', absolute: false));

    Auth::logout();
    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(route('home', absolute: false));

    Auth::logout();
    $this->post('/login', ['email' => $tech->email, 'password' => 'password'])
        ->assertRedirect(route('home', absolute: false));
});

it('throttles login after five failed attempts', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post('/login', ['email' => $user->email, 'password' => 'fout-wachtwoord'])
            ->assertRedirect()
            ->assertSessionHasErrors('email');
    }

    $response = $this->post('/login', ['email' => $user->email, 'password' => 'fout-wachtwoord']);
    $response->assertRedirect()->assertSessionHasErrors('email');
    $message = (string) collect(session('errors')->get('email'))->first();
    // Dutch ("seconden") and English ("seconds") share this fragment.
    expect($message)->toContain('econden');
});

it('ignores role and block mass-assignment on registration', function () {
    $response = $this->post('/register', [
        'name' => 'Sneaky Admin',
        'email' => 'sneaky@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'admin',
        'is_blocked' => true,
    ]);

    $response->assertRedirect(route('home', absolute: false));
    $user = User::where('email', 'sneaky@example.com')->firstOrFail();
    expect($user->role)->toBe('user')
        ->and((bool) $user->is_blocked)->toBeFalse();
    expect($user->klantnummer)->toStartWith('SLP-');
});

it('validates registration input', function () {
    $this->post('/register', [])->assertSessionHasErrors(['name', 'email', 'password']);

    User::factory()->create(['email' => 'dubbel@example.com']);
    $this->post('/register', [
        'name' => 'X',
        'email' => 'dubbel@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');

    $this->post('/register', [
        'name' => 'X',
        'email' => 'nieuw@example.com',
        'password' => 'password',
        'password_confirmation' => 'anders',
    ])->assertSessionHasErrors('password');
});

it('refuses login for blocked users and logs out blocked sessions', function () {
    $user = User::factory()->create(['is_blocked' => true]);

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect()
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    // A session that turns blocked mid-visit is kicked to login.
    $user->forceFill(['is_blocked' => false])->save();
    $this->actingAs($user)->get('/mijn-bestellingen')->assertOk();
    $user->forceFill(['is_blocked' => true])->save();
    $this->get('/mijn-bestellingen')->assertRedirect('/login');
    $this->assertGuest();
});

it('lets technicians and admins open any client payment page, but never guests or users', function () {
    $client = User::factory()->create();
    $url = '/technician/payment/'.$client->klantnummer;

    $this->get($url)->assertRedirect(route('login'));
    $this->getJson($url)->assertUnauthorized();
    $this->actingAs(User::factory()->create())->get($url)->assertRedirect(route('technician.login'));
    $this->actingAs(auditMatrixTech())->get($url)->assertOk();
    $this->actingAs(auditMatrixAdmin())->get($url)->assertOk();

    // JSON operator endpoints answer 401/403 for outsiders (web callers get a redirect).
    Auth::logout();
    $this->postJson('/technician/quote', ['klantnummer' => $client->klantnummer])->assertUnauthorized();
    $this->postJson('/technician/check-coupon', ['klantnummer' => $client->klantnummer])->assertUnauthorized();
    $this->actingAs(User::factory()->create())
        ->postJson('/technician/quote', ['klantnummer' => $client->klantnummer])->assertForbidden();
});

it('returns 404 for unknown klantnummers even for technicians', function () {
    $this->actingAs(auditMatrixTech())->get('/technician/payment/SLP-000000')->assertNotFound();
});
