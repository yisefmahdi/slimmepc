<?php

use App\Mail\AdminContactNotification;
use App\Mail\ContactReceived;
use App\Models\ContactSubmission;
use App\Models\User;
use App\Services\InboundContactFetcher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

// Tests never touch the real IMAP mailbox (the admin middleware + sync
// endpoint call the fetcher on every admin request).
function disableInboundImap(): void
{
    config(['contact-inbox.imap.username' => '', 'contact-inbox.imap.password' => '']);
}

function validContactPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Test Persoon',
        'email' => 'klant@voorbeeld.nl',
        'phone' => '0612345678',
        'subject' => 'reparatie',
        'request_type' => 'reparatie',
        'message' => 'Mijn computer start niet meer op. Kunnen jullie mij helpen?',
        'privacy_consent' => '1',
    ], $overrides);
}

it('stores a contact submission and sends the confirmation e-mail', function () {
    Mail::fake();
    disableInboundImap();

    $this->post('/contact/submit', validContactPayload())
        ->assertStatus(201)
        ->assertJson(['message' => 'Bedankt! Je bericht is verzonden.']);

    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'klant@voorbeeld.nl',
        'status' => 'new',
        'subject' => 'reparatie',
    ]);

    $submission = ContactSubmission::first();
    expect($submission)->not->toBeNull();

    Mail::assertSent(ContactReceived::class, fn ($mail) => $mail->submission->id === $submission->id);
    Mail::assertSent(AdminContactNotification::class, fn ($mail) => $mail->submission->id === $submission->id);
});

it('stores an attachment when provided', function () {
    Mail::fake();
    Storage::fake('local');
    disableInboundImap();

    $this->post('/contact/submit', validContactPayload([
        'attachment' => UploadedFile::fake()->create('factuur.pdf', 100, 'application/pdf'),
    ]), ['Accept' => 'application/json'])->assertStatus(201);

    $submission = ContactSubmission::first();
    expect($submission->attachment)->not->toBeNull();

    Storage::disk('local')->assertExists('contact/'.$submission->id.'/'.$submission->attachment);
});

it('rejects an invalid submission with 422', function () {
    $this->post('/contact/submit', [
        'name' => '',
        'email' => 'geen-email',
        'message' => 'te kort',
    ], ['Accept' => 'application/json'])->assertStatus(422);
});

it('rejects submissions where the honeypot field is filled', function () {
    $this->post('/contact/submit', validContactPayload([
        'website' => 'http://spam.example',
    ]), ['Accept' => 'application/json'])->assertStatus(422);
});

it('marks a submission as replied and sends the reply e-mail after an admin reply', function () {
    Mail::fake();
    disableInboundImap();

    $submission = ContactSubmission::create(validContactPayload());
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->postJson("/admin/contact-inbox/{$submission->id}/reply", [
            'body' => 'Goedemiddag, wij hebben uw bericht ontvangen.',
        ])
        ->assertOk();

    $this->assertDatabaseHas('contact_replies', [
        'contact_submission_id' => $submission->id,
        'sender' => 'admin',
        'source' => 'dashboard',
    ]);

    expect($submission->fresh()->status)->toBe('replied');

    Mail::assertSent(\App\Mail\ContactReplyMail::class);
});

it('sync endpoint pulls inbound replies (no IMAP configured) and returns counts', function () {
    Mail::fake();
    disableInboundImap();

    $admin = User::factory()->create(['role' => 'admin']);
    ContactSubmission::create(validContactPayload());

    $this->actingAs($admin)
        ->postJson('/admin/contact-inbox/sync')
        ->assertOk()
        ->assertJsonStructure(['counts' => ['new', 'total', 'unread']])
        ->assertJsonPath('counts.new', 1)
        ->assertJsonPath('counts.unread', 1);
});

it('marks a thread as read when the admin opens it and clears the unread badge', function () {
    disableInboundImap();

    $submission = ContactSubmission::create(validContactPayload());
    $admin = User::factory()->create(['role' => 'admin']);

    expect($submission->fresh()->unreadCount())->toBe(1);

    $this->actingAs($admin)->getJson("/admin/contact-inbox/{$submission->id}")->assertOk();

    expect($submission->fresh()->admin_read_at)->not->toBeNull();
    expect($submission->fresh()->unreadCount())->toBe(0);
});

it('sorts the list by latest activity and exposes the last message + unread count', function () {
    disableInboundImap();

    $older = ContactSubmission::create(validContactPayload(['name' => 'Oud']));
    $newer = ContactSubmission::create(validContactPayload(['name' => 'Nieuw']));
    $brandNew = ContactSubmission::create(validContactPayload(['name' => 'Nieuwste']));

    $now = now();
    $older->created_at = $now->copy()->subMinutes(10);
    $older->save();
    $newer->created_at = $now->copy()->subMinutes(5);
    $newer->save();
    $brandNew->created_at = $now->copy()->subMinutes(1);
    $brandNew->save();

    // The "older" thread gets an inbound reply (last activity), so it must rank first —
    // even though the "brand new" submission was created more recently.
    $reply = \App\Models\ContactReply::create([
        'contact_submission_id' => $older->id,
        'sender' => 'customer',
        'body' => 'Late reactie op de oudste aanvraag',
        'source' => 'inbound',
    ]);
    $reply->created_at = $now;
    $reply->save();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->getJson('/admin/contact-inbox/data')
        ->assertOk()
        ->assertJsonPath('data.0.id', $older->id)
        ->assertJsonPath('data.0.unread', 2)
        ->assertJsonPath('data.0.last_message.body', 'Late reactie op de oudste aanvraag')
        // A fresh submission with no replies ranks by its created_at (top, above older threads).
        ->assertJsonPath('data.1.id', $brandNew->id)
        ->assertJsonPath('data.1.unread', 1)
        ->assertJsonPath('data.2.id', $newer->id)
        ->assertJsonPath('data.2.unread', 1);
});

it('strips quoted reply text and signatures from inbound e-mail bodies', function () {
    $fetcher = new InboundContactFetcher();

    $english = "sdsdfsdsd\n\nOn Sun, Aug 16, 2026 at 11:01 PM slimmepc <yyyooo2004@gmail.com> wrote:\n> slimmepc <http://localhost:8000>\n> Hallo Yousef Ziad Mahdi,\n> ;dt;\n> Met vriendelijke groet,";

    expect($fetcher->cleanBody($english))->toBe('sdsdfsdsd');

    $dutch = "akkoord\n\nOp zo 16 aug 2026 om 23:01 schreef slimmepc <yyyooo2004@gmail.com>:\n> Hallo,\n> lvpfh";

    expect($fetcher->cleanBody($dutch))->toBe('akkoord');
});

it('keeps Arabic UTF-8 text intact while cleaning quoted bodies', function () {
    $fetcher = new InboundContactFetcher();

    $arabic = "كيفك\n\nفي الاثنين، 17 أغسطس 2026 في 3:53 م تمت كتابة ما يلي بواسطة slimmepc <yyyooo2004@gmail.com>:\n> مرحبا";

    $clean = $fetcher->cleanBody($arabic);

    expect($clean)->toBe('كيفك');
    expect(mb_check_encoding($clean, 'UTF-8'))->toBeTrue();
});

it('renders the inbox page as a two-pane chat shell', function () {
    disableInboundImap();

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/contact-inbox')
        ->assertOk()
        ->assertSee('id="inboxListPane"', false)
        ->assertSee('id="inboxChatPane"', false)
        ->assertSee('id="inboxThread"', false);
});

it('runs the inbound fetcher at most once per minute on admin pages', function () {
    disableInboundImap();

    $fetcher = Mockery::mock(InboundContactFetcher::class);
    $fetcher->shouldReceive('run')->once()->andReturn(['processed' => 0, 'matched' => 0, 'errors' => []]);
    $this->app->instance(InboundContactFetcher::class, $fetcher);

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    $this->actingAs($admin)->get('/admin/dashboard')->assertOk();

    Mockery::close();
});