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
        ->assertJsonStructure(['counts' => ['new', 'total']])
        ->assertJsonPath('counts.new', 1);
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