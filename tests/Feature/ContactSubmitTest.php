<?php

use App\Mail\AdminContactNotification;
use App\Mail\ContactReceived;
use App\Models\ContactSubmission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

it('stores a contact submission and queues the confirmation e-mail', function () {
    Mail::fake();

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

    Mail::assertQueued(ContactReceived::class, fn ($mail) => $mail->submission->id === $submission->id);
    Mail::assertQueued(AdminContactNotification::class, fn ($mail) => $mail->submission->id === $submission->id);
});

it('stores an attachment when provided', function () {
    Mail::fake();
    Storage::fake('local');

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

it('marks a submission as replied and queues the reply e-mail after an admin reply', function () {
    Mail::fake();

    $submission = ContactSubmission::create(validContactPayload());
    $admin = \App\Models\User::factory()->create(['role' => 'admin']);

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

    Mail::assertQueued(\App\Mail\ContactReplyMail::class);
});