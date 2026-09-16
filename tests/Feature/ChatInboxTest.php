<?php

use App\Models\Category;
use App\Models\ChatConversation;
use App\Models\User;
use App\Services\Chat\ChatProductSearch;
use Illuminate\Support\Facades\Storage;

function makeChatAdmin(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

function makeThread(array $over = []): ChatConversation
{
    $c = ChatConversation::create(array_merge([
        'guest_token' => Str::random(64),
        'name' => 'Test Klant',
        'email' => 'klant@test.nl',
        'status' => 'open',
        'last_activity_at' => now(),
    ], $over));
    $c->messages()->create(['sender' => 'customer', 'body' => 'Hallo, ik zoek een laptop', 'source' => 'widget']);

    return $c;
}

it('renders the chat inbox for admins', function () {
    $this->actingAs(makeChatAdmin())
        ->get('/admin/chat/inbox')
        ->assertOk()
        ->assertSee('Live Chat');
});

it('redirects guests away from the chat inbox', function () {
    $this->get('/admin/chat/inbox')->assertRedirect('/login');
});

it('uses the gate email for all mails even when logged in', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'email' => 'account@example.com',
    ]);

    $json = $this->actingAs($user)->postJson('/ai-chat/start', [
        'name' => 'Jan de Vries',
        'email' => 'jan.echt@example.com',
    ])->assertCreated()->json();

    $c = ChatConversation::find($json['conversation_id']);
    expect($c->email)->toBe('jan.echt@example.com')
        ->and($c->name)->toBe('Jan de Vries')
        ->and($c->user_id)->toBe($user->id);
});

it('requires gate email from everyone including logged-in users', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->role = 'user';
    $user->save();

    $this->actingAs($user)->postJson('/ai-chat/start', [
        'name' => 'Jan',
    ])->assertStatus(422)->assertJsonValidationErrors('email');
});

it('returns inbox data with last message, unread and counts', function () {
    makeChatAdmin();
    $c = makeThread();

    $res = $this->actingAs(User::where('role', 'admin')->first())
        ->getJson('/admin/chat/inbox/data')
        ->assertOk();

    $json = $res->json();
    expect($json['data'])->toHaveCount(1)
        ->and($json['data'][0]['unread'])->toBe(1)
        ->and($json['data'][0]['last_message']['body'])->toContain('laptop')
        ->and($json['counts']['open'])->toBe(1)
        ->and($c->fresh()->admin_read_at)->toBeNull();
});

it('marks thread read on show and exposes rating', function () {
    $this->actingAs(makeChatAdmin());
    $c = makeThread(['status' => 'closed', 'rating' => 5, 'rating_comment' => 'Top!']);

    $json = $this->getJson('/admin/chat/inbox/'.$c->id)->assertOk()->json();

    expect($json['conversation']['rating'])->toBe(5)
        ->and($json['conversation']['rating_comment'])->toBe('Top!')
        ->and($json['messages'])->toHaveCount(1)
        ->and($c->fresh()->admin_read_at)->not->toBeNull();
});

it('admin reply disables AI and promotes ai threads', function () {
    $this->actingAs(makeChatAdmin());
    $c = makeThread(['status' => 'ai', 'ai_enabled' => true]);

    Mail::fake();

    $this->postJson('/admin/chat/inbox/'.$c->id.'/reply', ['body' => 'Wij kijken ernaar.'])
        ->assertOk()
        ->assertJsonFragment(['ai_enabled' => false]);

    expect($c->fresh()->ai_enabled)->toBeFalse()
        ->and($c->fresh()->status)->toBe('open');
});

it('admin reply always mails the customer (open threads too)', function () {
    $this->actingAs(makeChatAdmin());
    $c = makeThread(['status' => 'open']);

    Mail::fake();

    $this->postJson('/admin/chat/inbox/'.$c->id.'/reply', ['body' => 'Hallo!'])->assertOk();

    Mail::assertSent(App\Mail\ChatReplyMail::class, 1);
});

it('admin reply mails offline threads', function () {
    $this->actingAs(makeChatAdmin());
    $off = makeThread(['status' => 'offline']);

    Mail::fake();

    $this->postJson('/admin/chat/inbox/'.$off->id.'/reply', ['body' => 'We zijn er weer.'])->assertOk();

    Mail::assertSent(App\Mail\ChatReplyMail::class, 1);
});

it('toggles AI per conversation', function () {
    $this->actingAs(makeChatAdmin());
    $c = makeThread(['ai_enabled' => true]);

    $this->postJson('/admin/chat/inbox/'.$c->id.'/toggle-ai')->assertOk()->json();
    expect($c->fresh()->ai_enabled)->toBeFalse();
});

it('offline submit opens a handed_over ticket with localized thanks (no thread)', function () {
    Mail::fake();

    $json = $this->postJson('/ai-chat/offline', [
        'name' => 'Nacht Klant',
        'email' => 'nacht@test.nl',
        'message' => 'Mijn laptop start niet meer op',
    ])->assertCreated()->json();

    // E-mail-only: bedankje terug, géén token/thread om te openen.
    expect($json['thanks'] ?? null)->not->toBeNull()
        ->and($json['token'] ?? null)->toBeNull()
        ->and($json['messages'] ?? null)->toBeNull();

    $c = ChatConversation::where('email', 'nacht@test.nl')->firstOrFail();
    expect($c->status)->toBe('handed_over')
        ->and($c->ai_enabled)->toBeFalse()
        ->and($c->handed_over_at)->not->toBeNull()
        ->and($c->messages()->count())->toBe(2);

    Mail::assertSent(App\Mail\AdminChatNotification::class, 1);
    Mail::assertSent(App\Mail\ChatOfflineReceived::class, 1);
});

it('handover mails the customer a ticket confirmation', function () {
    Mail::fake();

    $c = makeThread(['status' => 'open']);
    $this->postJson('/ai-chat/handover', ['token' => $c->guest_token])->assertOk();

    Mail::assertSent(App\Mail\ChatTicketMail::class, 1);
    Mail::assertSent(App\Mail\AdminChatNotification::class, 1);
    expect($c->fresh()->status)->toBe('handed_over');
});

it('rejects admin replies on closed tickets', function () {
    $this->actingAs(makeChatAdmin());
    $c = makeThread(['status' => 'closed']);

    Mail::fake();

    $this->postJson('/admin/chat/inbox/'.$c->id.'/reply', ['body' => 'Te laat.'])
        ->assertStatus(422)
        ->assertJsonFragment(['message' => 'Ticket gesloten — heropen het gesprek eerst via de status.']);

    Mail::assertNothingSent();
});

it('sends thanks mail once when admin closes', function () {
    $admin = makeChatAdmin();

    Mail::fake();

    $c = makeThread(['status' => 'open']);
    $this->actingAs($admin)->postJson('/admin/chat/inbox/'.$c->id.'/status', ['status' => 'closed'])->assertOk();
    Mail::assertSent(App\Mail\ChatClosedMail::class, 1);
});

it('sends no second thanks mail when re-closing', function () {
    $admin = makeChatAdmin();

    // Al gesloten vóór het request (geen overgang → geen mail).
    $c = makeThread(['status' => 'closed']);

    Mail::fake();

    $this->actingAs($admin)->postJson('/admin/chat/inbox/'.$c->id.'/status', ['status' => 'closed'])->assertOk();
    Mail::assertNothingSent();
});

it('sends thanks mail when customer closes via widget', function () {
    makeChatAdmin();
    $c2 = makeThread(['status' => 'open']);

    Mail::fake();

    $this->postJson('/ai-chat/close', ['token' => $c2->guest_token])->assertOk();
    Mail::assertSent(App\Mail\ChatClosedMail::class, 1);
});

it('ticket notification mail carries +chat reply-to', function () {
    $c = makeThread(['status' => 'handed_over']);

    $mail = new App\Mail\AdminChatNotification($c, 'handover');
    $envelope = $mail->envelope();

    expect($envelope->replyTo)->toHaveCount(1)
        ->and($envelope->replyTo[0]->address)->toContain('+chat-'.$c->id.'@');
});

it('employee email reply is stored as admin and forwarded to customer', function () {
    config(['contact-inbox.notify_email' => 'notify@test.nl']);

    Mail::fake();

    $c = makeThread(['status' => 'handed_over', 'ai_enabled' => false]);

    $raw = "From: notify@test.nl\r\nTo: info+chat-".$c->id."@slimme-pc.nl\r\nSubject: Re: ticket\r\n"
        ."Content-Type: text/plain; charset=UTF-8\r\n\r\nWij gaan dit morgen voor je nakijken.\r\n";
    $mime = Webklex\PHPIMAP\Message::fromString($raw);

    $fetcher = new App\Services\InboundContactFetcher();
    $ref = new ReflectionMethod($fetcher, 'appendChatMessage');
    $ref->setAccessible(true);
    $ref->invoke($fetcher, $mime, $c->fresh());

    $row = $c->messages()->reorder()->orderByDesc('id')->first();
    expect($row->sender)->toBe('admin')
        ->and($row->source)->toBe('email')
        ->and($c->fresh()->ai_enabled)->toBeFalse();

    Mail::assertSent(App\Mail\ChatReplyMail::class, 1);
});

it('stores inbound attachments under the full chat path', function () {
    $c = makeThread(['status' => 'open']);

    $fakeAttachment = new class
    {
        public function getName(): string
        {
            return 'foto.jpg';
        }

        public function decodeName(string $name): string
        {
            return $name;
        }

        public function getDisposition(): string
        {
            return 'attachment';
        }

        public function getContent(): string
        {
            return 'fake-image-bytes';
        }
    };

    $fetcher = new App\Services\InboundContactFetcher();
    $ref = new ReflectionMethod($fetcher, 'storeChatAttachment');
    $ref->setAccessible(true);
    $path = $ref->invoke($fetcher, [$fakeAttachment], $c);

    expect($path)->toBe('chat/'.$c->id.'/inbound/foto.jpg')
        ->and(Storage::disk('local')->exists($path))->toBeTrue();

    Storage::disk('local')->deleteDirectory('chat/'.$c->id);
});

it('inbound chat mail appends to thread and reopens closed tickets', function () {
    $fetcher = new App\Services\InboundContactFetcher();

    $makeMime = function (string $to, string $body): Webklex\PHPIMAP\Message {
        $raw = "From: klant@test.nl\r\nTo: {$to}\r\nSubject: Re: chat\r\n"
            ."Content-Type: text/plain; charset=UTF-8\r\n\r\n{$body}\r\n";

        return Webklex\PHPIMAP\Message::fromString($raw);
    };

    // Simuleer: klant antwoordt per e-mail op gesloten ticket.
    $c = makeThread(['status' => 'closed']);

    $ref = new ReflectionMethod($fetcher, 'findChatToken');
    $ref->setAccessible(true);
    $mime = $makeMime('info+chat-'.$c->id.'@slimme-pc.nl', 'Bedankt voor de hulp!');
    expect($ref->invoke($fetcher, $mime))->toBe($c->id);

    $append = new ReflectionMethod($fetcher, 'appendChatMessage');
    $append->setAccessible(true);
    $append->invoke($fetcher, $mime, $c->fresh());

    $fresh = $c->fresh();
    // Gesloten thread heropent (open bij open winkel, anders offline).
    expect($fresh->status)->toBeIn(['open', 'offline'])
        ->and($fresh->status)->not->toBe('closed')
        ->and($fresh->messages()->where('source', 'inbound')->count())->toBe(1)
        ->and($fresh->messages()->where('source', 'inbound')->first()->body)->toContain('Bedankt');
});

it('product search returns closest in-stock match with real url', function () {
    $cat = Category::create(['name' => 'Laptops', 'status' => true, 'sort_order' => 0]);
    App\Models\Product::create([
        'category_id' => $cat->id, 'title' => 'HP 15s Laptop Test', 'slug' => 'hp-15s-test-'.Str::random(6),
        'brand' => 'HP', 'price' => 599, 'stock_status' => 'in_stock', 'status' => true,
        'description' => 'Betrouwbare HP laptop voor dagelijks gebruik.',
    ]);
    App\Models\Product::create([
        'category_id' => $cat->id, 'title' => 'Dell Budget Laptop Test', 'slug' => 'dell-budget-test-'.Str::random(6),
        'brand' => 'Dell', 'price' => 349, 'stock_status' => 'in_stock', 'status' => true,
        'description' => 'Goedkope Dell laptop voor studenten.',
    ]);

    // Het budget komt van de agent (tool-arg), niet uit de tekst.
    $result = (new ChatProductSearch())->search('ik zoek een laptop', 3, 600);

    expect($result['budget'])->toBe(600)
        ->and($result['products'])->not->toBeEmpty()
        ->and($result['products'][0]['title'])->toContain('HP 15s')
        ->and($result['products'][0]['url'])->toContain('/webshop/laptops/hp-15s-test-')
        ->and($result['products'][0]['in_stock'])->toBeTrue();
});
