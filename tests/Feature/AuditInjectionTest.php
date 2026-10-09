<?php

/*
 * Sub-agent D (Injection & Input Handling) regression tests.
 * Covers the fixes documented in audit/04-injection.md.
 * SQLite :memory: only — never touches slimmepc_2026.
 */

use App\Http\Controllers\Admin\Shop\FilesController;
use App\Models\Category;
use App\Models\Product;
use App\Models\RepairSubmission;
use App\Models\User;
use App\Support\HtmlSanitizer;
use App\Support\SafeFilename;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeInjectAdmin(): User
{
    $u = User::factory()->create(['email_verified_at' => now()]);
    $u->role = 'admin';
    $u->save();

    return $u;
}

function makeInjectCategory(): Category
{
    return Category::create([
        'name' => 'Inject Test '.Str::random(6),
        'status' => true,
        'sort_order' => 0,
    ]);
}

/* ---------- HtmlSanitizer ---------- */

it('strips scripts, event handlers and javascript urls from product html', function () {
    $dirty = '<p>Hallo <strong>wereld</strong></p>'
        .'<script>alert(1)</script>'
        .'<img src="x" onerror="alert(2)">'
        .'<a href="javascript:alert(3)">klik</a>'
        .'<a href="https://example.com" target="_blank">ok</a>'
        .'<iframe src="https://evil.test"></iframe>';

    $clean = HtmlSanitizer::productDescription($dirty);

    expect($clean)->not->toContain('<script')
        ->and($clean)->not->toContain('onerror')
        ->and($clean)->not->toContain('javascript:')
        ->and($clean)->not->toContain('<iframe')
        ->and($clean)->toContain('<strong>wereld</strong>')
        ->and($clean)->toContain('https://example.com')
        ->and($clean)->toContain('rel="noopener noreferrer"');
    expect(HtmlSanitizer::containsActiveContent($clean))->toBeFalse();
});

it('unwraps unknown tags but keeps their text', function () {
    $clean = HtmlSanitizer::productDescription('<p>Prijs <marquee>koop nu</marquee>!</p>');

    expect($clean)->not->toContain('<marquee')
        ->and($clean)->toContain('koop nu');
});

/* ---------- SafeFilename ---------- */

it('neutralizes traversal and separator payloads in external filenames', function () {
    foreach (['../../.env', '..\\..\\windows\\win.ini', '/etc/passwd', 'a/b/c.pdf'] as $evil) {
        $safe = SafeFilename::fromExternal($evil);
        expect($safe)->not->toContain('/')
            ->and($safe)->not->toContain('\\')
            ->and($safe)->not->toContain('..');
    }

    expect(SafeFilename::fromExternal('.htaccess'))->not->toStartWith('.');
    expect(SafeFilename::fromExternal(''))->not->toBe('');
    // A normal localized filename stays recognizable.
    expect(SafeFilename::fromExternal('Factuur maart (2).pdf'))->toContain('Factuur');
});

/* ---------- Repair inbox photo: traversal + whitelist ---------- */

it('serves only whitelisted repair photos and blocks traversal', function () {
    Storage::fake('local');
    $admin = makeInjectAdmin();

    $sub = RepairSubmission::create([
        'repair_number' => 'SP-2026-00001',
        'device' => 'Laptop',
        'problems' => ['Start niet op'],
        'description' => 'Gaat niet aan.',
        'brand' => 'HP',
        'data_importance' => 'Nee',
        'opened_before' => 'Nee',
        'name' => 'Jan',
        'email' => 'jan@example.nl',
        'phone' => '0612345678',
        'postcode' => '1234 AB',
        'delivery_method' => 'Naar de winkel brengen',
        'contact_preference' => 'E-mail',
        'privacy' => true,
        'photos' => ['a1b2c3.jpg'],
    ]);
    Storage::disk('local')->put('repair/'.$sub->id.'/a1b2c3.jpg', 'fake-image');
    // Precondition guard: fail loudly here (not as a misleading 404 below)
    // if the fake disk ever misbehaves on this host.
    expect(Storage::disk('local')->exists('repair/'.$sub->id.'/a1b2c3.jpg'))->toBeTrue();

    $base = '/admin/reparatie-aanmeldingen/'.$sub->id.'/photo';

    // Legit photo streams.
    $this->actingAs($admin)->get($base.'/a1b2c3.jpg')->assertOk();

    // Unknown filename (even if it exists on disk elsewhere) → 404.
    Storage::disk('local')->put('repair/'.$sub->id.'/stray.jpg', 'x');
    $this->actingAs($admin)->get($base.'/stray.jpg')->assertNotFound();

    // Traversal segment → 404.
    $this->actingAs($admin)->get($base.'/..')->assertNotFound();
});

/* ---------- Product images: SVG blocked ---------- */

it('rejects svg product images on the public disk', function () {
    Storage::fake('public');
    $admin = makeInjectAdmin();
    $cat = makeInjectCategory();

    $svg = UploadedFile::fake()->createWithContent(
        'evil.svg',
        '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
    );

    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $cat->id,
        'title' => 'Inject SVG '.Str::random(6),
        'price' => 99.99,
        'status' => true,
        'main_image' => $svg,
    ])->assertStatus(422)->assertJsonValidationErrors('main_image');
});

/* ---------- Product description sanitized on save ---------- */

it('sanitizes the product description on store', function () {
    $admin = makeInjectAdmin();
    $cat = makeInjectCategory();
    $title = 'Inject Desc '.Str::random(6);

    $this->actingAs($admin)->postJson('/admin/webshop/products', [
        'category_id' => $cat->id,
        'title' => $title,
        'price' => 49.99,
        'status' => true,
        'description' => '<p>Mooie <strong>laptop</strong></p><script>alert(1)</script>'
            .'<a href="javascript:alert(2)">x</a>',
    ])->assertCreated();

    $stored = Product::where('title', $title)->firstOrFail()->description;
    expect($stored)->toContain('<strong>laptop</strong>')
        ->and($stored)->not->toContain('<script')
        ->and($stored)->not->toContain('javascript:');
});

/* ---------- Chunked upload caps ---------- */

it('caps chunk counts and total file size', function () {
    Storage::fake('local');
    $admin = makeInjectAdmin();

    $chunk = UploadedFile::fake()->createWithContent('p.bin', str_repeat('x', 1024));

    // Absurd chunk count → 422 at validation.
    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/chunk', [
        'upload_id' => 'cap-1',
        'index' => 0,
        'total' => FilesController::MAX_CHUNKS + 1,
        'chunk' => $chunk,
    ])->assertStatus(422);

    // Declared size above the absolute ceiling → 422.
    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/complete', [
        'upload_id' => 'cap-2',
        'total' => 1,
        'name' => 'big.iso',
        'size' => FilesController::MAX_FILE_SIZE + 1,
    ])->assertStatus(422);

    // Declared size that cannot fit in the declared chunks → 422.
    $this->actingAs($admin)->postJson('/admin/webshop/bestanden/complete', [
        'upload_id' => 'cap-3',
        'total' => 1,
        'name' => 'lie.iso',
        'size' => 9 * 1024 * 1024,
    ])->assertStatus(422)->assertJsonPath('message', 'Opgegeven bestandsgrootte past niet bij het aantal delen.');
});

/* ---------- Track endpoint throttling ---------- */

it('throttles the public track endpoint', function () {
    // Throttle runs before validation, so empty (instant-302) payloads still
    // consume quota — this keeps the test far under the 60s decay window.
    // (Valid payloads hit a MySQL-only CONCAT/LPAD query that 500s on the
    // SQLite test DB; reported separately in audit/04-injection.md.)
    $codes = [];
    for ($i = 0; $i < 10; $i++) {
        $codes[] = $this->post('/track', [])->getStatusCode();
    }

    $codes[] = $this->post('/track', [])->getStatusCode();
    expect(end($codes))->toBe(429);
    expect($codes[0])->not->toBe(429);
});

/* ---------- Afspraak honeypot ---------- */

it('rejects afspraak submissions with the honeypot filled', function () {
    $this->postJson('/afspraak/submit', [
        'name' => 'Bot',
        'email' => 'bot@example.nl',
        'street' => 'Straat',
        'phone' => '0612345678',
        'postcode' => '1234 AB',
        'house_number' => '1',
        'city' => 'Apeldoorn',
        'device' => 'Laptop',
        'problem' => 'Bot probleemomschrijving hier.',
        'preferred_date' => now()->addDay()->toDateString(),
        'preferred_time' => '09:00 - 11:00',
        'website' => 'http://spam.test',
    ])->assertStatus(422)->assertJsonValidationErrors('website');
});

/* ---------- Admin JS output encoding tripwire ---------- */

it('escapes the product image path in the admin detail modal', function () {
    $js = file_get_contents(public_path('assets/js/admin/products.js'));

    expect($js)->toContain('escapeHtml(p.main_image)');
});

/* ---------- Mailing subject header guard ---------- */

it('rejects newlines in the mass-mail subject', function () {
    $admin = makeInjectAdmin();

    $this->actingAs($admin)->post('/admin/mailinglijst/verzenden', [
        'recipients' => ['a@example.nl'],
        'subject' => "Hi\r\nBcc: evil@example.nl",
        'message' => 'inhoud',
    ])->assertSessionHasErrors('subject');
});
