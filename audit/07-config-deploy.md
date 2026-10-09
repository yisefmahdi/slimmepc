# SlimmePC Audit — 07 Config, Infrastructure & Deployment Hardening (Sub-agent G, 2026-10-09)

Scope: `.env.example`, `config/*.php`, `public/.htaccess`, `scripts/deploy.sh`, `scripts/*.php` guards,
queue/scheduler/mail, DB constraints, seeders, `composer.json`/`package.json` (report only), Node/Vite build.
Branch `audit/full-review`. Local: PHP 8.2.8, Node v24.21.0, Laragon/Windows. Server: Hostinger (not accessed).

## Findings

### [G-01] Legacy DB dump, sync backups and credential files were committable
- Severity: Critical
- Category: Data Leak
- Location: `.gitignore:1` (root; `h_00094667_slimmepc.sql` 649KB real-customer PII untracked, `scripts/backups/` untracked)
- Description / Proof: `.gitignore` had no `*.sql`, `scripts/backups/` or `db.json` rule — `git check-ignore`
  returned nothing for the dump before the fix. One stray `git add -A` would have committed real PII.
- Impact: GDPR data leak via GitHub history (practically unerasable once pushed).
- Fix applied: `.gitignore` now ignores `*.sql`, `*.dump`, `/scripts/backups/`, `db.json`, `*-db.json`,
  `*-db-credentials.json`. Verified: `git check-ignore -v` matches all three classes (commit pending, see footer).
- Regression test: `tests/Feature/AuditConfigTest.php` → "gitignores database dumps…"
- Status: Fixed

### [G-02] `public/.htaccess` lacked dotfile/dump blocks, headers and hardened listing control
- Severity: High
- Category: Config
- Location: `public/.htaccess:1`
- Description / Proof: File contained only the Laravel rewrite block (`Options -MultiViews -Indexes` nested
  inside `mod_negotiation`, so it vanished if that module was absent). No rules blocking `.env`, `.git`,
  `*.sql`, `*.log`, no security headers, no HTTPS note.
- Impact: Direct download of secrets/dumps/logs if ever placed under docroot; clickjacking/MIME-sniffing
  exposure; directory listing if negotiation module missing.
- Fix applied: `FilesMatch` deny for dotfiles + `composer.*` + `.(env|sql|dump|log|sqlite)` (Apache 2.2/2.4
  compatible), top-level `Options -Indexes`, `X-Content-Type-Options` / `X-Frame-Options: SAMEORIGIN` /
  `Referrer-Policy` / `Permissions-Policy` via mod_headers, comment delegating HTTPS to Hostinger "Force HTTPS"
  so local `http://` dev keeps working (commit pending).
- Regression test: `tests/Feature/AuditConfigTest.php` → "htaccess blocks dotfiles…" + "htaccess sets baseline security headers"
- Status: Fixed

### [G-03] `scripts/deploy.sh`: cron overlap, no maintenance mode, no cache rebuild, failures ignored
- Severity: High
- Category: Config
- Location: `scripts/deploy.sh:1-71` (pre-fix)
- Description / Proof: No `flock` (cron re-entry races itself); no `artisan down/up` (users hit mid-migration
  schema); only `optimize:clear` — `config:cache route:cache view:cache` never rebuilt, so production ran on
  uncached config/routes/views; `composer install` failure was logged and the script **continued to migrate**
  (new code against old vendor); `migrate` failure was logged and the script continued to sitemap/cache-clear.
  No rollback documentation.
- Impact: Race-corrupted deploys, 500s during migrate, slow/unoptimized production, half-applied releases.
- Fix applied: `flock -n` overlap lock (skip + log), `artisan down/up` around migrate with `fail()` helper that
  always brings the app back up, abort (`exit 1`) on composer/migrate failure, `config:cache route:cache
  view:cache` rebuild, sitemap kept non-fatal, rollback procedure in header comment using logged commit hashes
  (commit pending). Note: no `bash -n` available on this Windows box — run `bash -n scripts/deploy.sh` once on
  the server; only standard `flock(1)` + artisan commands are used.
- Regression test: `tests/Feature/AuditConfigTest.php` → lock / maintenance / cache tests
- Status: Fixed

### [G-04] `.env.example` had no production checklist and missed cookie/log variables
- Severity: Medium
- Category: Config
- Location: `.env.example:1`
- Description / Proof: `APP_DEBUG=true` + `APP_ENV=local` with no production guidance; `SESSION_SECURE_COOKIE`,
  `SESSION_HTTP_ONLY`, `SESSION_SAME_SITE`, `LOG_DAILY_DAYS` not present at all, so a server `.env` cloned from
  the example would run non-secure cookies and unbounded `single` log growth without anyone noticing.
- Impact: Production misconfiguration by copy-paste (debug on, insecure cookies, disk-filling logs).
- Fix applied: production checklist header block (APP_ENV/APP_DEBUG/APP_URL/SESSION_*/LOG_*/CACHE/QUEUE/MAIL +
  Hostinger Force-HTTPS note), added `SESSION_SECURE_COOKIE=null`, `SESSION_HTTP_ONLY=true`,
  `SESSION_SAME_SITE=lax`, `LOG_DAILY_DAYS=14` (commit pending).
- Regression test: `tests/Feature/AuditConfigTest.php` → "env example documents required production values"
- Status: Fixed

### [G-05] `SESSION_SECURE_COOKIE` defaults to null → cookies not Secure unless server sets the var
- Severity: Medium
- Category: Config
- Location: `config/session.php:172`
- Description / Proof: `'secure' => env('SESSION_SECURE_COOKIE')` with no default (Laravel skeleton standard).
  Verified secure/http_only/same_site otherwise sane (`http_only` default true, `same_site` default `lax`,
  lifetime 120, driver database). If the production `.env` omits the var, session cookies go out over plaintext.
- Impact: Session hijack on any non-HTTPS hop; also breaks behind Hostinger proxy if scheme detection is off (see G-08).
- Fix applied: documented (`SESSION_SECURE_COOKIE=true` in `.env.example` + checklist item). Code default
  intentionally untouched — forcing `true` in code would break local `http://` sessions (browsers reject Secure
  cookies over http). Test asserts `http_only=true`, `same_site=lax` defaults.
- Regression test: `tests/Feature/AuditConfigTest.php` → "session cookies default…"
- Status: Partially fixed

### [G-06] `AdminUserSeeder` hardcodes a documented password and resets it on every seed
- Severity: High
- Category: Config
- Location: `database/seeders/AdminUserSeeder.php:28` (`'password' => 'slimmepc@@#@10'`)
- Description / Proof: `updateOrCreate(['email' => 'slimmepc@admin.com'], [… 'password' => 'slimmepc@@#@10'])`.
  The `hashed` cast (`app/Models/User.php:55`) means it is hashed at rest — not plaintext in DB — but the
  secret is weak, checked into git, and documented in `project-structure.md` (per `audit/00-inventory.md` §red-flag 6).
  Any `db:seed` in production silently resets the admin password to the public value.
- Impact: Full admin takeover by anyone with repo/docs access if seeding ever runs against production.
- Fix applied: Not fixed — seeder is outside the config-scope fix allowance and `deploy.sh` (fixed version)
  never seeds, so the trigger path is deployment, not code I may touch.
- Regression test: none (would require asserting another agent's file).
- Status: Needs Human Decision — recommended: read password from `env('ADMIN_INITIAL_PASSWORD')`, skip when
  `APP_ENV=production` unless explicitly forced, rotate the current admin password, remove it from docs.

### [G-07] `scripts/sync-*.php` + `verify-sync.php` have no CLI-only guard
- Severity: Medium
- Category: Config
- Location: `scripts/sync-phase1.php:1`, `scripts/sync-phase2.php:1`, `scripts/sync-phase3-orders.php:1`,
  `scripts/sync-phase3-contact.php`, `scripts/sync-mailing-list.php`, `scripts/sync-purchase-records.php`,
  `scripts/sync-license-codes.php`, `scripts/verify-sync.php` — `Select-String 'php_sapi_name|PHP_SAPI'` finds
  zero guards (only incidental "client" comments match).
- Description / Proof: Scripts truncate/refill production-shaped tables on `--yes`. If the scripts directory is
  ever reachable via web (docroot misconfig, backup copy under `public/`), they become remote DB-wipe endpoints.
  Mitigations present: `--dry-run` default-safe flow, pre-run mysqldump to `scripts/backups/`, interactive
  confirmation without `--yes`, `--config=db.json` so passwords avoid the shell history.
- Impact: DB destruction / PII handling outside SSH if exposed over HTTP.
- Fix applied: Not fixed — reason: `scripts/sync-phase*.php` are dirty in another workstream (`git status` shows
  `M scripts/sync-phase1.php, sync-phase2.php, sync-phase3-orders.php`); editing risks conflicts.
- Regression test: none (other workstream's files).
- Status: Open — recommended one-liner at the top of each script (after `declare`):
  `if (php_sapi_name() !== 'cli') { http_response_code(404); exit; }`, plus server check that `scripts/` lives
  outside `public_html`, and prefer `--config=db.json` over `--pass=` (CLI args are visible in `ps`).

### [G-08] No trusted-proxy configuration — scheme/IP detection unreliable behind Hostinger
- Severity: Medium
- Category: Config
- Location: `bootstrap/app.php:21` (no `trustProxies` configured); grep for `TrustProxies|trustProxies|TrustHosts`
  across `app/`, `bootstrap/`, `config/` returns nothing.
- Description / Proof: Hostinger terminates TLS at its edge; without trusted proxies `$request->secure()` /
  `isSecure()` can report http behind https, which defeats `SESSION_SECURE_COOKIE=true` and canonical URL
  generation. `SECURE` cookie + `sitemap:generate` absolute URLs both depend on correct scheme detection.
- Impact: Secure cookies silently dropped, mixed-content/canonical-URL bugs, wrong client IPs in logs/rate limits.
- Fix applied: Not fixed — reason: trusting `*` blindly enables `X-Forwarded-*` spoofing; correct CIDR list needs
  Hostinger's egress ranges (human input). Do NOT edit payment/auth-adjacent request pipeline from this workstream.
- Regression test: none.
- Status: Needs Human Decision — recommended: `$middleware->trustProxies(at: ['<hostinger-ranges>'])` (or
  `trustProxies('*')` only if the server is unreachable except via the Hostinger proxy), then verify
  `https://…/up` + a Secure-flagged session cookie in production.

### [G-09] `composer audit`: 6 advisories on 4 packages (incl. 2 High on league/commonmark)
- Severity: High (2×) / Medium (1×) / Low (3×)
- Category: Dependency
- Location: `composer.json:8` (`laravel/framework ^12.0` @ 12.65.0 local), transitive `league/commonmark`,
  `firebase/php-jwt` (via `kreait/firebase-php`), `league/flysystem`
- Description / Proof (`composer audit --no-dev`, 2026-10-09):
  - `league/commonmark` High — quadratic-time DoS in GFM table block-start scan (GHSA-3q6v-r5mr-hxv8, ≤2.10.1)
  - `league/commonmark` High — DoS via distinctly-named attributes (GHSA-8rr7-cvq3-gmfh, <2.10.0)
  - `league/commonmark` Medium — DisallowedRawHtml bypass on tag-name-suffixed literals (GHSA-97jj-33gv-5xf9)
  - `laravel/framework` Low — XSS in Debug page info (GHSA-jh5r-qr3c-85q8, affects <12.69.0; local is 12.65.0 —
    only reachable with APP_DEBUG=true, cf. G-04/G-16)
  - `firebase/php-jwt` Low — weak encryption (CVE-2025-45769, <7.0.0)
  - `league/flysystem` Low — path-normalizer bypass via malformed UTF-8 (CVE-2026-102601, ≤3.35.2)
  `npm audit --omit=dev`: 0 vulnerabilities. `barryvdh/laravel-dompdf: "*"` is an unconstrained version
  (hygiene; Agent A).
- Impact: Markdown DoS vectors are the sharpest (user/FAQ content may render Markdown); framework XSS needs
  debug mode. No evidence of exploitation; mail/chat flows do not render attacker Markdown server-side (Agent D
  owns output-encoding confirmation).
- Fix applied: Not fixed — `composer.json`/`package.json` owned by Agent A per work order.
- Regression test: none.
- Status: Open

### [G-10] Log defaults: `single` driver + `debug` level grow unbounded and may capture PII
- Severity: Low
- Category: Config
- Location: `config/logging.php:61`, `.env.example:18-21`
- Description / Proof: `LOG_STACK=single` default writes `storage/logs/laravel.log` forever (no rotation);
  `daily` channel with `LOG_DAILY_DAYS=14` exists but is opt-in. Default level `debug`. Code is stock Laravel —
  correct to leave for dev.
- Impact: Disk exhaustion on long-lived server; debug logs can retain PII (mails use `log` mailer in dev only).
- Fix applied: `.env.example` now ships `LOG_DAILY_DAYS=14` + checklist (`LOG_STACK=daily`, `LOG_LEVEL=warning`).
  `deploy.sh` truncates its own `deploy.log` at 5MB (pre-existing, kept).
- Regression test: covered by "env example documents…" test.
- Status: Partially fixed

### [G-11] Queue is `sync`: mail failures throw inside the web request; `failed_jobs` exists but unused
- Severity: Low
- Category: Config
- Location: `.env.example:48` (`QUEUE_CONNECTION=sync`), `config/queue.php:123` (`failed.driver=database-uuids`,
  table `failed_jobs` migrated in `0001_01_01_000002`), `routes/console.php:12` (only `sitemap:generate` daily)
- Description / Proof: `.env.example` comment states sync mail is intentional (no worker required). Consequence:
  an SMTP outage fails the request the user just submitted (contact/order) instead of retrying. `failover`
  mailer (smtp→log) is defined in `config/mail.php:82` but is not the default. No queue worker/scheduler
  configured on the server (cron only runs `deploy.sh` per DEPLOYMENT.md).
- Impact: Mail outage = user-facing 500s; no retry/audit trail via `failed_jobs`.
- Fix applied: Not fixed — architectural choice, works as documented; changing to database queue needs a
  worker + cron entry (human decision).
- Regression test: none.
- Status: Needs Human Decision — at minimum set server cron for `schedule:run` (sitemap) and consider making
  `failover` the production `MAIL_MAILER` so a dead SMTP degrades to log instead of 500.

### [G-12] Node/Vite: local Node v24 works; "Node 18 vs Vite ≥20.19" note is stale-but-harmless
- Severity: Info
- Category: Config
- Location: `package.json:21` (`vite ^7.0.7`, resolved 7.3.6), `vite.config.js`
- Description / Proof: `node -v` → v24.21.0; `npm run build` → success in 1m18s (58 modules,
  `public/build/manifest.json` + css/js emitted); `npm audit --omit=dev` → 0 vulns. Vite 7 requires Node ≥18,
  satisfied. Upgrade path: Vite 8 needs Node ≥20.19/22.12 — also satisfied by Node 24, so no action needed;
  Hostinger does not build assets (no `npm` step in `deploy.sh` — build artifacts must be committed or built
  in CI; `public/build` is gitignored, so verify the release pipeline publishes them).
- Impact: none locally; release-pipeline question for production assets.
- Fix applied: none needed.
- Regression test: none (build verified manually).
- Status: Open — Needs Human Decision: confirm `public/build/*` reaches the server (CI build or force-add
  manifest); otherwise production serves stale/missing Vite assets.

### [G-13] Migration integrity: all 55 migrations pass on a clean DB; rollback/re-migrate OK
- Severity: Info
- Category: Config
- Location: `database/migrations/` (0001×3 … `2026_10_06_*` digital files)
- Description / Proof: `migrate --force` against a clean TEMP sqlite file — all 55 ran DONE, zero errors;
  `migrate:rollback --step=3` + re-`migrate` clean; TEMP file deleted afterwards. NEVER touched `slimmepc_2026`.
  Down-methods present (incl. `dropIfExists` for jobs/cache tables).
- Impact: deploy-time `migrate --force` is safe to run.
- Fix applied: n/a.
- Regression test: n/a (manual integrity run).
- Status: Fixed (verified)

### [G-14] DB constraints healthy: uniques, FKs and indexes present; no NOT NULL drift spotted
- Severity: Info
- Category: Config
- Location: `database/migrations/*.php` (sampled all 55 files via grep)
- Description / Proof: uniques on `users.email`, `users.klantnummer` (nullable), `repair_number`,
  `afspraak_number`, `manual_invoices.invoice_number`, `order_number`, `order_invoices.invoice_number`,
  `memberships.klantnummer`, `membership_invoices.invoice_number`, `mollie_payment_id`, `cart_token`,
  `category/product/shipping slug`, `products.sku` (nullable), `coupons.code`, `content_blocks[page,section,key]`,
  `chat_closed_days.closed_at`, `chat_conversations.guest_token`, `favorites[user,product]`,
  `cart_items[cart,product]`; FKs with `cascadeOnDelete`/`nullOnDelete` on carts/items/orders/addresses/reviews/
  chat/technician/membership/device-photos; indexes on status/email/created_at/payment/order columns.
- Impact: none — invoice/klantnummer/slug collisions and orphan rows are structurally prevented.
- Fix applied: none needed.
- Regression test: none.
- Status: Fixed (verified)

### [G-15] Seeders use non-destructive upserts; only G-06 breaks the pattern
- Severity: Info
- Category: Config
- Location: `database/seeders/*.php`, `DatabaseSeeder.php:17`
- Description / Proof: All seeders use `firstOrCreate`/`updateOrCreate`; zero `truncate`/`delete` outside sync
  scripts. `DatabaseSeeder` calls admin/content/legal/membership/technician/shipping/FAQ seeders only
  (no `DetailedProductsSeeder` fecundity run, no weak defaults besides G-06).
- Impact: `db:seed` is re-runnable without data loss — except it resets the admin password (G-06).
- Fix applied: none (see G-06).
- Regression test: none.
- Status: Partially fixed (pattern verified; G-06 outstanding)

### [G-16] Production `.env` values (APP_DEBUG, APP_URL, mail, keys) cannot be verified from here
- Severity: Medium
- Category: Config
- Location: server `.env` (no SSH access from audit box); local `.env` is `APP_ENV=local`, Debug ENABLED
  (correct for dev — `php artisan about` confirms)
- Description / Proof: `config/app.php:42` (`debug` ← `APP_DEBUG`, default false) and `config/services.php`
  (Mollie/OpenAI purely `env()`-driven, no unsafe defaults — source-asserted in test) are code-correct; but
  whether the Hostinger `.env` actually sets `APP_DEBUG=false`, canonical `APP_URL`, real `MAIL_*` and live
  `MOLLIE_KEY` is unverifiable locally. No secrets were printed/copied/committed during this audit.
- Impact: A single `APP_DEBUG=true` on production re-exposes stack traces (and the G-09 framework XSS).
- Fix applied: checklist in `.env.example` (G-04); no server change possible from here.
- Regression test: "third-party keys come from env…" (source-level).
- Status: Needs Human Decision — verify server `.env` against the checklist in this report.

### [G-17] Chunked upload stays under PHP limits; no total-size cap on assembly (cross-ref Agent D)
- Severity: Info
- Category: Performance
- Location: `app/Http/Controllers/Admin/Shop/FilesController.php:15` (`CHUNK_MAX_KB=8192`), local
  `upload_max_filesize=post_max_size=128M`
- Description / Proof: 8MB chunks to the private `local` disk (`storage/app/private`, never web-served —
  test-asserted), `upload_id` regex + per-chunk `max`, `disk_free_space` pre-check on assembly, 24h stale-chunk
  cleanup. `complete.total` allows up to 100000 chunks (~800GB theoretical) with no aggregate cap — flagged for
  Agent D (validation) rather than fixed here.
- Impact: none on config; cutoff question belongs to upload validation.
- Fix applied: none (out of scope).
- Regression test: private-disk assertion in `AuditConfigTest`.
- Status: Open (handed to Agent D)

## Configuration & Deployment Hardening Checklist

- ✅ `.gitignore` blocks `*.sql`, `*.dump`, `/scripts/backups/`, `db.json` (+ variants) — G-01
- ✅ `public/.htaccess` denies dotfiles/`.env`/`composer.*`/`*.sql,*.dump,*.log,*.sqlite` — G-02
- ✅ `public/.htaccess` sets `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` — G-02
- ✅ `public/.htaccess` has top-level `Options -Indexes` — G-02
- ⚠️ HTTPS redirect lives in Hostinger panel ("Force HTTPS"), not `.htaccess` — verify ON — G-02/G-16
- ✅ `scripts/deploy.sh` has `flock` cron-overlap lock — G-03
- ✅ `scripts/deploy.sh` uses `down`/`up` and aborts on composer/migrate failure — G-03
- ✅ `scripts/deploy.sh` rebuilds `config:cache route:cache view:cache` — G-03
- ⚠️ `bash -n scripts/deploy.sh` must be run once on the server (no bash on audit box) — G-03
- ⚠️ Rollback = `git reset --hard <prev-hash>` + migrate + cache rebuild (hashes in deploy.log) — no auto-rollback — G-03
- ✅ `.env.example` documents the full production checklist — G-04
- ⚠️ Server `.env` must set: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://…`,
  `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`,
  `LOG_STACK=daily`, `LOG_LEVEL=warning`, `MAIL_MAILER=smtp` + creds, live `MOLLIE_KEY` — G-05/G-10/G-16
- ❌ Trusted proxies unconfigured — scheme/IP detection unreliable behind Hostinger edge — G-08
- ❌ `AdminUserSeeder` hardcoded documented password — rotate + env-drive + prod guard — G-06
- ❌ `scripts/*.php` lack CLI-only guards; must live outside `public_html` — G-07
- ❌ `league/commonmark` 2 High + 1 Medium advisories; framework/php-jwt/flysystem lows — Agent A — G-09
- ✅ Session defaults: database driver, 120min lifetime, http-only, lax — G-05
- ✅ Secrets purely env-driven, no hardcoded defaults (Mollie/OpenAI/AWS/Firebase) — G-16
- ✅ `local` disk = `storage/app/private` (never web-served); `public` disk via `/storage` symlink — G-17
- ✅ Vuln-relevant cron gotcha: Hostinger disables `passthru/system/shell_exec/popen` — deploy uses artisan
  only via shell cron (unaffected), but any PHP feature shelling out will fail on the server — noted, no occurrence found in deploy path
- ✅ `failed_jobs` + `job_batches` tables migrated; queue intentionally `sync` (documented trade-off) — G-11
- ✅ Scheduler ships only `sitemap:generate` daily — add `schedule:run` cron if scheduling matters — G-11
- ✅ Upload limits: chunks 8MB ≪ 128M PHP limits; private disk; stale-chunk GC — G-17
- ✅ Migrations: 55/55 clean on sqlite + rollback/re-migrate verified (TEMP file, since deleted) — G-13
- ✅ DB uniques/FKs/indexes (klantnummer, invoice numbers, slugs, emails) verified — G-14
- ✅ Seeders non-destructive upserts (except G-06) — G-15
- ✅ `npm run build` green on Node v24.21.0 + Vite 7.3.6; `npm audit` 0 vulns — G-12
- ⚠️ Confirm `public/build/*` reaches Hostinger (gitignored; needs CI build or force-add) — G-12
