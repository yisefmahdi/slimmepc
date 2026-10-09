<?php

use App\Models\AdminPushLog;
use App\Models\FcmToken;
use App\Models\User;
use App\Services\AdminPushNotifier;
use Illuminate\Support\Facades\Mail;

function makePushAdmin(array $overrides = []): User
{
    $user = User::factory()->create($overrides);
    $user->role = 'admin';
    $user->save();

    return $user->fresh();
}

it('skips push gracefully without firebase credentials and audits it', function () {
    config(['firebase.credentials' => '']);

    $result = AdminPushNotifier::notify(
        'contact', '1', 'Titel', 'Body', 'https://example.com/admin'
    );

    expect($result['targeted'])->toBe(0)
        ->and($result['delivered'])->toBe(0);

    $this->assertDatabaseHas('admin_push_logs', [
        'type' => 'contact',
        'ref_id' => '1',
        'targeted' => 0,
    ]);
});

it('lets an admin register a device token', function () {
    $admin = makePushAdmin();

    $this->actingAs($admin)->postJson('/admin/notificaties/tokens', [
        'token' => 'fcm-token-abc123',
        'platform' => 'web',
        'device_label' => 'Test Browser',
    ])->assertOk()->assertJson(['message' => 'Notificaties ingeschakeld op dit apparaat.']);

    $this->assertDatabaseHas('fcm_tokens', [
        'user_id' => $admin->id,
        'token' => 'fcm-token-abc123',
        'platform' => 'web',
    ]);
});

it('rejects invalid token payloads', function () {
    $admin = makePushAdmin();

    $this->actingAs($admin)->postJson('/admin/notificaties/tokens', [
        'platform' => 'windows-phone',
    ])->assertStatus(422);
});

it('lets an admin delete only their own token', function () {
    $admin = makePushAdmin();
    $other = makePushAdmin(['email' => 'ander@voorbeeld.nl']);

    $mine = FcmToken::create(['user_id' => $admin->id, 'token' => 'tok-mine', 'platform' => 'web']);
    $theirs = FcmToken::create(['user_id' => $other->id, 'token' => 'tok-theirs', 'platform' => 'android']);

    $this->actingAs($admin)->deleteJson('/admin/notificaties/tokens/'.$theirs->id)
        ->assertForbidden();

    $this->actingAs($admin)->deleteJson('/admin/notificaties/tokens/'.$mine->id)
        ->assertOk();

    $this->assertDatabaseMissing('fcm_tokens', ['token' => 'tok-mine']);
    $this->assertDatabaseHas('fcm_tokens', ['token' => 'tok-theirs']);
});

it('blocks non-admins from the notificaties page', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/admin/notificaties')->assertForbidden();
});

it('returns 422 for test push without devices', function () {
    $admin = makePushAdmin();

    $this->actingAs($admin)->postJson('/admin/notificaties/test')
        ->assertStatus(422);
});

it('renders the notificaties page for admins', function () {
    config(['contact-inbox.imap.username' => '', 'contact-inbox.imap.password' => '']);
    $admin = makePushAdmin();

    $this->actingAs($admin)->get('/admin/notificaties')->assertOk();
});

it('serves the firebase service worker as javascript', function () {
    $this->get('/firebase-messaging-sw.js')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/javascript')
        ->assertSee('onBackgroundMessage', false)
        ->assertSee('notificationclick', false);
});

it('audits a push alongside the contact submit', function () {
    Mail::fake();
    config(['firebase.credentials' => '']);
    config(['contact-inbox.imap.username' => '', 'contact-inbox.imap.password' => '']);
    config(['contact-inbox.notify_email' => 'admin@voorbeeld.nl']);

    $this->post('/contact/submit', [
        'name' => 'Push Test',
        'email' => 'klant@voorbeeld.nl',
        'phone' => '0612345678',
        'subject' => 'reparatie',
        'request_type' => 'reparatie',
        'message' => 'Mijn computer start niet meer op. Kunnen jullie mij helpen?',
        'privacy_consent' => '1',
    ])->assertStatus(201);

    // afterResponse callbacks run in tests; the push is skipped (no creds) but audited.
    $this->assertDatabaseHas('admin_push_logs', ['type' => 'contact']);
    expect(AdminPushLog::where('type', 'contact')->count())->toBeGreaterThanOrEqual(1);
});
