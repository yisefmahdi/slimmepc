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

it('stores an attachment sent by the admin from the dashboard', function () {
    Mail::fake();
    Storage::fake('local');
    disableInboundImap();

    $submission = ContactSubmission::create(validContactPayload());
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->post("/admin/contact-inbox/{$submission->id}/reply", [
            'body' => 'Hierbij de offerte.',
            'attachment' => UploadedFile::fake()->create('offerte.pdf', 100, 'application/pdf'),
        ])
        ->assertOk()
        ->assertJsonPath('reply.attachment', fn ($v) => str_starts_with((string) $v, 'outbound/'));

    $reply = $submission->fresh()->replies()->where('sender', 'admin')->first();
    expect($reply->attachment)->toStartWith('outbound/');

    $files = Storage::disk('local')->allFiles('contact/'.$submission->id.'/outbound');
    expect($files)->toHaveCount(1);
    expect(str_ends_with($files[0], '.pdf'))->toBeTrue();
});

it('attaches the dashboard file to the admin reply e-mail', function () {
    disableInboundImap();

    $submission = ContactSubmission::create(validContactPayload());

    Storage::disk('local')->put('contact/'.$submission->id.'/outbound/test.pdf', '%PDF');

    $mail = new \App\Mail\ContactReplyMail($submission, 'Hallo', 'Admin', 'outbound/test.pdf');

    $attachments = $mail->attachments();
    expect($attachments)->toHaveCount(1);
    expect($attachments[0]->as)->toBe('test.pdf');

    Storage::disk('local')->deleteDirectory('contact/'.$submission->id);
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

it('streams an attachment attached to an inbound reply and exposes it in the thread', function () {
    disableInboundImap();
    Storage::fake('local');

    $submission = ContactSubmission::create(validContactPayload(['name' => 'Met bijlage']));
    $reply = \App\Models\ContactReply::create([
        'contact_submission_id' => $submission->id,
        'sender' => 'customer',
        'body' => 'Zie bijlage',
        'attachment' => 'inbound/schema.png',
        'source' => 'inbound',
    ]);

    Storage::disk('local')->put('contact/'.$submission->id.'/inbound/schema.png', 'fake-image-bytes');

    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get('/admin/contact-inbox/reply/'.$reply->id.'/attachment')
        ->assertOk()
        ->assertDownload('schema.png');

    $this->actingAs($admin)
        ->getJson('/admin/contact-inbox/'.$submission->id)
        ->assertJsonPath('replies.0.attachment', 'inbound/schema.png');
});

it('stores inbound reply attachments (PDF + inline image) and strips [image:] placeholders from the body', function () {
    disableInboundImap();
    Storage::fake('local');

    $pdf = base64_encode('%PDF-1.4 fake pdf content');
    $png = base64_encode("\x89PNG\r\n\x1a\nfake-png-bytes");

    $mime = "From: klant@voorbeeld.nl\r\n"
        ."To: slimmepc+reply-999@example.com\r\n"
        ."Subject: Re: help\r\n"
        ."Message-ID: <pdf-test-1@voorbeeld.nl>\r\n"
        ."Date: ".now()->format('r')."\r\n"
        ."MIME-Version: 1.0\r\n"
        ."Content-Type: multipart/mixed; boundary=\"BOUNDARY123\"\r\n"
        ."\r\n"
        ."--BOUNDARY123\r\n"
        ."Content-Type: text/plain; charset=\"UTF-8\"\r\n"
        ."\r\n"
        ."Hallo, hierbij de foto.\r\n"
        ."[image: ed84c59d-6bc0-4b32-a220-dd77621048da.jpg]\r\n"
        ."En de offerte.\r\n"
        ."--BOUNDARY123\r\n"
        ."Content-Type: image/jpeg; name=\"foto.jpg\"\r\n"
        ."Content-Disposition: inline; filename=\"foto.jpg\"\r\n"
        ."Content-ID: <ed84c59d-6bc0-4b32-a220-dd77621048da@example.com>\r\n"
        ."Content-Transfer-Encoding: base64\r\n"
        ."\r\n"
        .$png."\r\n"
        ."--BOUNDARY123\r\n"
        ."Content-Type: application/pdf; name=\"offerte.pdf\"\r\n"
        ."Content-Disposition: attachment; filename=\"offerte.pdf\"\r\n"
        ."Content-Transfer-Encoding: base64\r\n"
        ."\r\n"
        .$pdf."\r\n"
        ."--BOUNDARY123--";

    $message = \Webklex\PHPIMAP\Message::fromString($mime);

    expect($message->getAttachments()->count())->toBe(2);

    $fetcher = app(InboundContactFetcher::class);
    $method = new ReflectionMethod($fetcher, 'appendReply');
    $submission = ContactSubmission::create(validContactPayload(['name' => 'Bijlage uit e-mail']));

    $method->invoke($fetcher, $message, $submission);

    $reply = $submission->fresh()->replies->first();

    expect($reply)->not->toBeNull();
    // Attachment-disposition files win over inline images (a follow-up e-mail
    // quoting the previous message re-sends the old inline image), and the
    // [image:] placeholder is gone from the body.
    expect($reply->body)->not->toContain('[image:');
    expect($reply->attachment)->toBe('inbound/offerte.pdf');
    expect(Storage::disk('local')->exists('contact/'.$submission->id.'/inbound/offerte.pdf'))->toBeTrue();
});

it('keeps only the PDF attachment when an e-mail has no inline image', function () {
    disableInboundImap();
    Storage::fake('local');

    $pdf = base64_encode('%PDF-1.4 fake pdf content');

    $mime = "From: klant@voorbeeld.nl\r\n"
        ."To: slimmepc+reply-998@example.com\r\n"
        ."Subject: Re: offerte\r\n"
        ."Message-ID: <pdf-test-2@voorbeeld.nl>\r\n"
        ."Date: ".now()->format('r')."\r\n"
        ."MIME-Version: 1.0\r\n"
        ."Content-Type: multipart/mixed; boundary=\"B2\"\r\n"
        ."\r\n"
        ."--B2\r\n"
        ."Content-Type: text/plain; charset=\"UTF-8\"\r\n"
        ."\r\n"
        ."Hierbij de offerte.\r\n"
        ."--B2\r\n"
        ."Content-Type: application/pdf; name=\"offerte.pdf\"\r\n"
        ."Content-Disposition: attachment; filename=\"offerte.pdf\"\r\n"
        ."Content-Transfer-Encoding: base64\r\n"
        ."\r\n"
        .$pdf."\r\n"
        ."--B2--";

    $message = \Webklex\PHPIMAP\Message::fromString($mime);

    expect($message->getAttachments()->count())->toBe(1);

    $fetcher = app(InboundContactFetcher::class);
    $method = new ReflectionMethod($fetcher, 'appendReply');
    $submission = ContactSubmission::create(validContactPayload(['name' => 'PDF uit e-mail']));

    $method->invoke($fetcher, $message, $submission);

    $reply = $submission->fresh()->replies->first();

    expect($reply->attachment)->toBe('inbound/offerte.pdf');
    expect(Storage::disk('local')->exists('contact/'.$submission->id.'/inbound/offerte.pdf'))->toBeTrue();
});

it('prefers the PDF over a re-sent screenshot even when the image is also Content-Disposition: attachment', function () {
    disableInboundImap();
    Storage::fake('local');

    // Replicates a real Gmail reply: the previous message's screenshot is
    // re-attached (both as "attachment" disposition) and the new PDF is the
    // file the customer actually sent. The PDF must win.
    $png = base64_encode("\x89PNG\r\n\x1a\nfake-png-bytes");
    $pdf = base64_encode('%PDF-1.4 fake pdf content');

    $mime = "From: klant@voorbeeld.nl\r\n"
        ."To: slimmepc+reply-997@example.com\r\n"
        ."Subject: Re: diagnose\r\n"
        ."Message-ID: <dup-test@voorbeeld.nl>\r\n"
        ."Date: ".now()->format('r')."\r\n"
        ."MIME-Version: 1.0\r\n"
        ."Content-Type: multipart/mixed; boundary=\"DUP\"\r\n"
        ."\r\n"
        ."--DUP\r\n"
        ."Content-Type: text/plain; charset=\"UTF-8\"\r\n"
        ."\r\n"
        ."Zie de nieuwe offerte.\r\n"
        ."--DUP\r\n"
        ."Content-Type: image/jpeg; name=\"ed84c59d-6bc0-4b32-a220-dd77621048da (1).jpg\"\r\n"
        ."Content-Disposition: attachment; filename=\"ed84c59d-6bc0-4b32-a220-dd77621048da (1).jpg\"\r\n"
        ."Content-Transfer-Encoding: base64\r\n"
        ."\r\n"
        .$png."\r\n"
        ."--DUP\r\n"
        ."Content-Type: application/pdf; name=\"offerte.pdf\"\r\n"
        ."Content-Disposition: attachment; filename=\"offerte.pdf\"\r\n"
        ."Content-Transfer-Encoding: base64\r\n"
        ."\r\n"
        .$pdf."\r\n"
        ."--DUP--";

    $message = \Webklex\PHPIMAP\Message::fromString($mime);

    expect($message->getAttachments()->count())->toBe(2);

    $fetcher = app(InboundContactFetcher::class);
    $method = new ReflectionMethod($fetcher, 'appendReply');
    $submission = ContactSubmission::create(validContactPayload(['name' => 'Duplicaat test']));

    $method->invoke($fetcher, $message, $submission);

    $reply = $submission->fresh()->replies->first();

    expect($reply->attachment)->toBe('inbound/offerte.pdf');
    expect(Storage::disk('local')->exists('contact/'.$submission->id.'/inbound/offerte.pdf'))->toBeTrue();
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