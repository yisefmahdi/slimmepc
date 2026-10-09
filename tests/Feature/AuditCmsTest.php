<?php

/*
 * Sub-agent F (testing audit): CMS section/design updates, upload validation,
 * cache invalidation and guest-only page-cache isolation.
 */

use App\Models\User;
use App\Support\Cms;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

function auditCmsAdmin(): User
{
    $u = User::factory()->create();
    $u->role = 'admin';
    $u->save();

    return $u;
}

it('saves a section and publishes it to guests', function () {
    $admin = auditCmsAdmin();
    $before = Cms::version();

    $this->travel(2)->seconds();
    $res = $this->actingAs($admin)->postJson('/admin/content/home/section/hero', [
        'blocks' => [
            'title_line1' => 'Audit Held Titel',
            'description' => 'Audit beschrijving voor de held.',
        ],
    ])->assertOk()->assertJsonPath('saved.title_line1', 'Audit Held Titel');

    $this->assertDatabaseHas('content_blocks', [
        'page' => 'home', 'section' => 'hero', 'block_key' => 'title_line1', 'value' => 'Audit Held Titel',
    ]);
    expect(Cms::get('home', 'hero', 'title_line1'))->toBe('Audit Held Titel');
    expect(Cms::version())->not->toBe($before);

    // Guests see the new content on the rendered page.
    $this->get('/')->assertOk()->assertSee('Audit Held Titel');
});

it('rejects unknown sections and ignores unknown block keys', function () {
    $admin = auditCmsAdmin();

    $this->actingAs($admin)->postJson('/admin/content/spookpagina/section/hero', ['blocks' => ['title_line1' => 'x']])->assertNotFound();
    $this->actingAs($admin)->postJson('/admin/content/home/section/spooksectie', ['blocks' => ['title_line1' => 'x']])->assertNotFound();
    $this->actingAs($admin)->get('/admin/content/spookpagina/section/hero')->assertNotFound();

    $this->actingAs($admin)->postJson('/admin/content/home/section/hero', [
        'blocks' => ['bestaat_niet' => 'sneaky', 'title_line1' => 'Echte Titel'],
    ])->assertOk();
    $this->assertDatabaseMissing('content_blocks', ['block_key' => 'bestaat_niet']);

    $this->actingAs($admin)->postJson('/admin/content/home/section/hero', [])->assertStatus(422);
});

it('restricts the CMS editor to admins', function () {
    $this->get('/admin/content/design')->assertRedirect('/login');
    $this->postJson('/admin/content/design', ['design' => ['site' => ['meta_title' => 'x']]])
        ->assertStatus(401);

    $user = User::factory()->create();
    $tech = User::factory()->create(['role' => 'technician']);
    foreach ([$user, $tech] as $actor) {
        $this->actingAs($actor)->get('/admin/content/design')->assertForbidden();
        $this->actingAs($actor)->postJson('/admin/content/home/section/hero', ['blocks' => ['title_line1' => 'x']])->assertForbidden();
        $this->actingAs($actor)->postJson('/admin/content/design', ['design' => ['site' => ['meta_title' => 'x']]])->assertForbidden();
        $this->actingAs($actor)->postJson('/admin/content/media', [])->assertForbidden();
    }
});

it('saves design settings and busts the cache', function () {
    $admin = auditCmsAdmin();
    $before = Cms::version();

    $this->travel(2)->seconds();
    $this->actingAs($admin)->postJson('/admin/content/design', [
        'design' => ['site' => ['meta_title' => 'Audit SEO Titel', 'meta_description' => 'Audit omschrijving']],
    ])->assertOk();

    expect(Cms::designValue('meta_title'))->toBe('Audit SEO Titel');
    expect(Cms::version())->not->toBe($before);

    $this->actingAs($admin)->postJson('/admin/content/design', [])->assertStatus(422);
});

it('validates CMS media uploads', function () {
    $admin = auditCmsAdmin();

    $image = UploadedFile::fake()->image('held.png', 800, 400);
    $res = $this->actingAs($admin)->postJson('/admin/content/media', ['file' => $image])->assertOk();
    $path = $res->json('path');
    expect($path)->toStartWith('assets/img/landing/');
    // Clean up the real public/ write so the repo stays untouched.
    @unlink(public_path($path));
    $this->assertFileDoesNotExist(public_path($path));

    $exe = UploadedFile::fake()->create('kwaad.php', 100, 'application/x-php');
    $this->actingAs($admin)->postJson('/admin/content/media', ['file' => $exe])->assertStatus(422);

    $this->actingAs($admin)->postJson('/admin/content/media', [])->assertStatus(422);
});

it('rejects bad image mimes inside section saves', function () {
    $admin = auditCmsAdmin();

    $exe = UploadedFile::fake()->create('kwaad.exe', 100, 'application/octet-stream');
    $response = $this->actingAs($admin)->post(
        '/admin/content/home/section/hero',
        [
            'blocks' => [
                'title_line1' => 'Blijft staan',
                'hero_image_file' => $exe,
            ],
        ],
        ['Accept' => 'application/json']
    );
    $response->assertStatus(422);
});

it('never serves the shared guest cache to authenticated users', function () {
    $user = User::factory()->create(['name' => 'Cache Privacy Persoon']);
    $version = Cms::version();

    // Prime the guest cache.
    $guestResponse = $this->get('/');
    $guestResponse->assertOk();
    $cached = Cache::get("cms.page.html.home.{$version}");
    expect($cached)->toBeString();

    // Authenticated users get a fresh personalized response, not the cache.
    $authResponse = $this->actingAs($user)->get('/');
    $authResponse->assertOk()->assertSee('Cache Privacy Persoon');
    expect($authResponse->getContent())->not->toBe($cached);

    // The shared cache holds no user data.
    expect($cached)->not->toContain('Cache Privacy Persoon')
        ->and($cached)->not->toContain($user->email);
});

it('documents the csrf meta token inside cached guest html (finding F-CMS-01, see report)', function () {
    $version = Cms::version();
    $this->get('/');
    $cached = (string) Cache::get("cms.page.html.home.{$version}");

    // The shared layout embeds <meta name="csrf-token">, so warmed guest
    // cache carries a session-bound token. Locked here so any caching or
    // layout change revisits the finding documented in audit/06-testing.md.
    expect($cached)->toContain('csrf-token');
});

it('serves uncached form pages with no-store headers', function () {
    $res1 = $this->get('/reparatie-aanmelden')->assertOk();
    expect($res1->headers->get('Cache-Control'))->toContain('no-store')->toContain('no-cache');

    $res2 = $this->get('/afspraak')->assertOk();
    expect($res2->headers->get('Cache-Control'))->toContain('no-store')->toContain('no-cache');

    // And nothing is written to the shared html cache for them.
    $version = Cms::version();
    expect(Cache::get("cms.page.html.reparatie.{$version}"))->toBeNull();
});
