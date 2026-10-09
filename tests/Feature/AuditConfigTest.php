<?php

// Audit G (config/deploy hardening) regression tests.
// File-content + config-default assertions only; no secrets involved.

it('gitignores database dumps, sync backups and db credential files', function () {
    $gitignore = file_get_contents(base_path('.gitignore'));

    expect($gitignore)->toContain('*.sql')
        ->toContain('backups')
        ->toContain('db.json');
});

it('htaccess blocks dotfiles, env files, dumps and logs', function () {
    $htaccess = file_get_contents(public_path('.htaccess'));

    expect($htaccess)->toContain('FilesMatch')
        ->toContain('.env')
        ->toContain('env|sql|dump|log')
        ->toContain('Require all denied')
        ->toContain('Options -Indexes');
});

it('htaccess sets baseline security headers', function () {
    $htaccess = file_get_contents(public_path('.htaccess'));

    expect($htaccess)->toContain('X-Content-Type-Options')
        ->toContain('X-Frame-Options')
        ->toContain('Referrer-Policy');
});

it('deploy script serializes cron runs with a lock', function () {
    $deploy = file_get_contents(base_path('scripts/deploy.sh'));

    expect($deploy)->toContain('flock');
});

it('deploy script uses maintenance mode and aborts on failure', function () {
    $deploy = file_get_contents(base_path('scripts/deploy.sh'));

    expect($deploy)->toContain('artisan down')
        ->toContain('artisan up')
        ->toContain('migrate --force');
});

it('deploy script rebuilds config, route and view caches', function () {
    $deploy = file_get_contents(base_path('scripts/deploy.sh'));

    expect($deploy)->toContain('config:cache')
        ->toContain('route:cache')
        ->toContain('view:cache');
});

it('env example documents required production values', function () {
    $example = file_get_contents(base_path('.env.example'));

    expect($example)->toContain('APP_DEBUG=false')
        ->toContain('SESSION_SECURE_COOKIE=true')
        ->toContain('LOG_STACK=daily');
});

it('session cookies default to http-only and lax same-site', function () {
    expect(config('session.http_only'))->toBeTrue()
        ->and(config('session.same_site'))->toEqual('lax');
});

it('third-party keys come from env with no hardcoded defaults', function () {
    // Assert on the config source so a locally populated .env cannot
    // mask a hardcoded fallback.
    $services = file_get_contents(config_path('services.php'));

    expect($services)->toContain("env('MOLLIE_KEY')")
        ->toContain("env('OPENAI_API_KEY')")
        ->not->toContain('test_')
        ->not->toContain('live_')
        ->not->toContain('sk-');
});

it('private disk stays outside the public docroot', function () {
    $norm = fn ($p) => str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string) $p);

    expect($norm(config('filesystems.disks.local.root')))
        ->toContain('app'.DIRECTORY_SEPARATOR.'private')
        ->and($norm(config('filesystems.disks.public.root')))
        ->toContain('app'.DIRECTORY_SEPARATOR.'public');
});
