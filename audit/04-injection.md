# SlimmePC Audit — 04 Injection & Input Handling (Sub-agent D)

Branch: `audit/full-review` · Date: 2026-10-09 · Stack: Laravel 12 + Blade, Alpine.js + jQuery, TinyMCE, DomPDF, chunked uploads, Mollie.
Sources: `audit/00-inventory.md`, `audit/routes.txt` (259 routes), full grep over `app/`, `resources/views/`, `public/assets/js/`, `routes/`, `config/`, plus `composer audit` / `npm audit`.

Method: every `DB::raw/whereRaw/selectRaw/orderByRaw/DB::statement`, every `{!! !!}` (23 sites), all upload handlers, all file-serving endpoints, CSRF exception list, outbound fetchers, `exec/shell/unserialize/eval`, mass-assignment, price/coupon math, mail headers, redirects, signed URLs. Fixes verified by targeted Pest tests on SQLite `:memory:` (never `slimmepc_2026`).

Severity counts (this report): Critical 1 · High 3 · Medium 6 · Low 7 · Info 2.
Fixed: Critical 1/1 · High 3/3 · Medium 2/6 · Low 5/7. Open / Needs Human Decision: 6 (all with reason + exact fix).

---

### [INJ-01] IMAP attachment filename traversal → arbitrary file write
- Severity: Critical
- Category: Injection
- Location: `app/Services/InboundContactFetcher.php:353` (`storeFirstAttachment`) and `:549` (`storeChatAttachment`); served by `Admin\ContactInboxController@replyAttachment` (`app/Http/Controllers/Admin/ContactInboxController.php:296`) and chat photo path
- Description: Attachment names from inbound e-mail are fully attacker-controlled (anyone can mail the inbox). The code stored them verbatim: `Storage::disk('local')->put($dir.'/'.$name, $content)` where `$name` is only MIME-decoded. A part named `../../../../.env` (or absolute/Windows paths) escapes `$dir` (`contact/{id}/inbound/`, `chat/{id}/inbound/`) and writes attacker content anywhere under `storage/app/` — and with enough `../` above it (project `.env`, i.e. remote secret overwrite / config injection). Served back via `replyAttachment` (`contact/{submission_id}/{attachment}`), same traversal class on read.
- Proof: code path pre-fix had zero basename/sanitization between `decodeMimeHeader()` and `put()`; `Storage::put` does not normalize `..`. Payload e-mail attachment name: `../../../../.env` → resolves to `<project>/.env`.
- Impact: unauthenticated remote file write (content + path partly controlled), follow-up read via admin attachment route; potential `.env` overwrite → app compromise.
- Fix applied (commit `b79b8af`): new `App\Support\SafeFilename::fromExternal()` (basename, NUL-strip, separator collapse, leading-dot strip, character whitelist incl. Unicode letters, length cap, random fallback; invariant: output never contains `/`, `\` or `..`) applied at both store sites.
- Regression test: `tests/Feature/AuditInjectionTest.php` → "neutralizes traversal and separator payloads" (`../../.env`, `..\..\windows\win.ini`, `/etc/passwd`, `a/b/c.pdf`, `.htaccess`, empty).
- Status: Fixed

### [INJ-02] Repair-inbox photo endpoint: path traversal + unscoped file read
- Severity: High
- Category: Injection
- Location: `app/Http/Controllers/Admin/RepairInboxController.php:131-143` (route `GET admin/reparatie-aanmeldingen/{repairSubmission}/photo/{file}`)
- Description: `$file` was concatenated unsanitized (`'repair/'.$id.'/'.$file`) and never checked against the submission's registered photos. Encoded `..%2F` segments (single-segment `{file}` blocks raw `/`, but `%2F` decoding is server-dependent) allow escaping `repair/{id}/` to read any `local`-disk file (other submissions' photos, contact attachments, chat photos); at minimum it was an unscoped read (any filename under the submission dir, not just registered ones).
- Proof: pre-fix `photo()` had no `basename()` and no membership check — request `GET /admin/reparatie-aanmeldingen/1/photo/..%2F..%2Fcontact%2F2%2Foutbound%2F<x>` pattern.
- Impact: authenticated (admin/tech role) cross-submission file disclosure; worst case local-disk file read.
- Fix applied (commit `b79b8af`): `basename()` + strict whitelist `in_array($file, $submission->photos, true)` → 404 otherwise. (Sibling endpoints already scoped: `DeviceReceiptController@photo` checks `device_receipt_id`, `LaptopLoanController@photo` checks `laptop_loan_id`, `ContactInboxController@attachment` uses server-generated uuid names, `ChatController@photo` binds token→conversation.)
- Regression test: "serves only whitelisted repair photos and blocks traversal" (legit 200; unregistered-but-present file 404; `..` 404).
- Status: Fixed

### [INJ-03] SVG product images stored on the public disk (stored XSS for every visitor)
- Severity: High
- Category: XSS
- Location: `app/Http/Controllers/Admin/Shop/ProductController.php:126,128,250,252` (store/update, main + gallery); rendered at `resources/views/landing/product-details.blade.php` (`/storage/...`)
- Description: `main_image`/`gallery_images.*` allowed `mimes:...,svg,...` and Laravel's `image` rule passes SVG. Files land on the `public` disk → web-reachable `/storage/products/...`. A crafted SVG (`<svg><script>…</script></svg>` or `<image href="javascript:…">`) executes in the origin of every product-page visitor (session theft, cart/checkout skimming). Category images were already safe (`jpg,jpeg,png,webp` only).
- Proof: pre-fix rule string `image|mimes:jpeg,png,jpg,gif,svg,webp,avif`; upload `evil.svg` with real SVG content passed validation and stored under `products/main`.
- Impact: stored XSS, site-wide victim pool.
- Fix applied (commit `b79b8af`): removed `svg` from all four product image rules.
- Regression test: "rejects svg product images on the public disk" (real SVG bytes via `createWithContent`, expects 422 on `main_image`; chosen so it passes `image` but fails `mimes` — distinguishes pre/post fix).
- Status: Fixed (residual: SVGs uploaded before this fix remain on disk — Needs Human Decision: one-off cleanup query + delete, see INJ-15)

### [INJ-04] Product description rendered raw without server-side sanitization (stored XSS)
- Severity: High
- Category: XSS
- Location: `resources/views/landing/product-details.blade.php:393` (`{!! $product->description !!}`); write path `Admin\Shop\ProductController@store/:152` + `@update/:274`; inputs: TinyMCE HTML, AI generator output (`AiProductController@generateDescription`), legacy imports
- Description: `description` validated only as `nullable|string` — `<script>`, `onerror=`, `javascript:` URLs, `<iframe>` stored verbatim and executed for every visitor. Any admin-account compromise, AI prompt-injection echo, or poisoned legacy row becomes site-wide XSS.
- Proof: pre-fix store accepted `<p>x</p><script>alert(1)</script><a href="javascript:alert(2)">x</a>` unchanged (no stripping anywhere in the write path; confirmed by test on pre-fix code path).
- Impact: stored XSS, all product-page visitors + admin preview.
- Fix applied (commit `b79b8af`): new dependency-free `App\Support\HtmlSanitizer::productDescription()` (DOM allowlist: formatting/headings/lists/links/images/tables; drops script/style/iframe/object/embed/form, all `on*`, `style`, unsafe URL schemes; images additionally allow only `data:image/*`; `rel="noopener noreferrer"` on `_blank`) applied in store + update.
- Regression test: two sanitizer unit tests + "sanitizes the product description on store" (201 + DB row contains `<strong>` but no `<script`/`javascript:`).
- Status: Fixed (residual: rows written before this fix are NOT cleaned — Needs Human Decision: one-off sanitize pass, see INJ-15)

### [INJ-05] Chunked upload: unbounded chunk count and file size (disk/inode exhaustion)
- Severity: Medium
- Category: Injection
- Location: `app/Http/Controllers/Admin/Shop/FilesController.php:chunk` (`:60-72`), `complete` (`:78-...`); routes `POST admin/webshop/bestanden/chunk|complete` (admin-only)
- Description: `total` accepted up to 100000 chunks × 8 MB ≈ 800 GB of declared chunks with no assembled-size ceiling (`size` only `min:1`). A compromised/stolen admin session (or CSRF from another still-exempt surface) could fill disk / exhaust inodes with chunk dirs; `pruneStaleChunks()` only runs on `complete`, so abandoned uploads accumulate. (`size` was at least verified against the assembled bytes — good.)
- Proof: pre-fix rules `'total' => max:100000`, `'size' => min:1` (no max); `putFileAs($dir, $chunk, (string)$index)` per chunk.
- Impact: admin-authenticated DoS via disk fill; limited blast radius (admin-only route, CSRF-protected).
- Fix applied (commit `b79b8af`): `MAX_CHUNKS = 2048`, `MAX_FILE_SIZE = 10 GB`, `index < total` check, and `size ≤ total × chunk-size` consistency check before touching disk. Legit ISOs (≤10 GB) unaffected.
- Regression test: "caps chunk counts and total file size" (total over cap 422; size over ceiling 422; size/chunks mismatch 422 with exact message).
- Status: Fixed (existing `DigitalFilesTest` 8/8 still green — no legit-flow breakage)

### [INJ-06] POST /track had no throttle (receipt enumeration, CSRF-exempt endpoint)
- Severity: Low
- Category: CSRF
- Location: `routes/web.php:164-168` (`TrackingController@track`); CSRF exemption `track/` in `bootstrap/app.php:42`
- Description: `POST /track` is CSRF-exempt (form posts without token enforcement) and had no `throttle` middleware, while every sibling public form has one (`contact/reparatie/afspraak` 5,1; reviews 5,1; checkout 10,1; ai-chat per-endpoint). `t_number + email` pairs could be brute-forced to enumerate device receipts (names, devices, serials, statuses shown on `tracking/status`).
- Proof: pre-fix route had zero middleware; `track()` answers differently for hit (200 + receipt view) vs miss (302 + error) → oracle.
- Impact: low-rate PII enumeration by unauthenticated callers.
- Fix applied (committed as `0bc565f` — authored by Sub-agent D, swept into Agent E's concurrent commit of `routes/web.php`; working tree verified identical to HEAD): `throttle:10,1` on `POST /track`. CSRF exemption intentionally kept (see INJ-11).
- Regression test: "throttles the public track endpoint" (11 rapid posts → 11th is 429).
- Status: Fixed

### [INJ-07] Mass-mail subject allowed header newlines (mail header injection)
- Severity: Low
- Category: Injection
- Location: `app/Http/Controllers/Admin/MailingListController.php:54` (`MailingListController@send` → `CustomEmail` subject)
- Description: `subject` rule was `required|string|max:255` with no newline guard; the value flows verbatim into the mail `Envelope` subject and is queued per recipient. (Admin-only input, Symfony Mime hardens most transports — defense in depth.)
- Proof: pre-fix validation accepted `"Hi\r\nBcc: evil@example.nl"`.
- Impact: admin-account abuse → header injection on outbound mail.
- Fix applied (commit `b79b8af`): `not_regex:/[\r\n]/` on `subject`. (`BroadcastMail` subjects are code-controlled `match()` — safe.)
- Regression test: "rejects newlines in the mass-mail subject" (`assertSessionHasErrors('subject')`).
- Status: Fixed

### [INJ-08] Afspraak form lacked the bot honeypot all sibling forms have
- Severity: Low
- Category: Quality
- Location: `app/Http/Requests/StoreAfspraakSubmissionRequest.php:38`
- Description: contact/repair/chat forms all enforce `'website' => ['prohibited']`; afspraak did not, leaving one spam channel without the shared bot tripwire. (Route already throttled 5,1.)
- Proof: pre-fix rules had no `website` key while `ContactController`, `RepairController`, `ChatController` all unset/validate it.
- Impact: spam-only, low.
- Fix applied (commit `b79b8af`): `'website' => ['prohibited']` (absent field passes; only bots fail — zero legit-impact).
- Regression test: "rejects afspraak submissions with the honeypot filled" (422 + `website` error).
- Status: Fixed

### [INJ-09] Admin product modal interpolated the image path unescaped
- Severity: Low
- Category: XSS
- Location: `public/assets/js/admin/products.js:315` (`openDetails`)
- Description: every other field in the template uses `escapeHtml()`; `p.main_image` was interpolated raw into `src="…"`. Path is server-generated today (low exploitability), but any future path influence (legacy import, stored value with `"`) becomes attribute-breakout XSS in the admin origin. (Sibling table row at line 79 already escaped — this was the single miss in ~15 audited admin JS files.)
- Proof: pre-fix `` `<img src="/storage/${p.main_image}"` `` vs escaped everywhere else in the same template literal.
- Impact: admin-origin XSS conditional on path control; defense in depth.
- Fix applied (commit `b79b8af`): `${escapeHtml(p.main_image)}`.
- Regression test: "escapes the product image path in the admin detail modal" (file tripwire asserting the escaped interpolation; no JS harness in repo).
- Status: Fixed

### [INJ-10] Login/register/logout CSRF-exempt (login-CSRF) — NOT FIXED
- Severity: Medium
- Category: CSRF
- Location: `bootstrap/app.php:34-44` (`login/`, `register/`, `logout/` in `validateCsrfTokens(except:)`)
- Description: Laravel does not exempt these by default. Exempting enables login-CSRF (attacker forces victim into attacker-owned account → victim may enter order/payment-adjacent data there) and logout-CSRF (nuisance). All auth forms DO render `@csrf`, and auth pages are never page-cached, so enforcement would work in production.
- Proof: exception list contains the three auth prefixes; every auth blade includes `@csrf` (verified).
- Impact: medium-low (login-CSRF needs follow-on social engineering; no session fixation — Laravel regenerates on login).
- Not fixed — reason: removing the exemption breaks `tests/Feature/Auth/*` (token-less posts, owned by Agent F's suite) and changes auth behavior; needs a coordinated change (drop exemptions + update auth tests to send tokens). I verified the revert is byte-identical (`git diff bootstrap/app.php` empty after a near-miss edit whose typo I caught and corrected before committing).
- Regression test: none (CSRF is skipped under `runningUnitTests`, so feature tests cannot assert 419s; verification is by code inspection).
- Status: Open — Needs Human Decision (with Agent F)

### [INJ-11] contact/track/ai-chat CSRF exemptions retained (page-cache constraint) — NOT FIXED
- Severity: Medium
- Category: CSRF
- Location: `bootstrap/app.php:34-44` (`contact/submit`, `track/`, `ai-chat/*`); cached pages `PageController@home/contact` (guest HTML cache incl. `<meta name="csrf-token">`)
- Description: these widgets live on full-page-cached HTML, so a strict token would be stale for guests (Agent C's caching domain). Removing exemptions now would 419 all cached-page submissions. Compensating controls verified present: throttle on every endpoint (contact/reparatie/afspraak 5,1; track 10,1 after INJ-06; ai-chat 5–60,1 per endpoint), honeypot `website=prohibited` (contact/repair/chat/afspraak), 64-char capability tokens for chat (`guest_token` size:64 validated per call), signed URLs for downloads.
- Proof: `ai-chat.js:101-102` reads the token from cached-page meta (stale by design); `contact.blade` has no `@csrf` (JS-driven, relies on exemption).
- Impact: residual CSRF on three public forms, mitigated by above controls; spam/phishing-message injection possible but rate-limited.
- Not fixed — reason: correct fix is "never cache CSRF tokens" (Agent C), then re-enable enforcement. Cross-agent decision.
- Regression test: throttle/honeypot tests (INJ-06/08) guard the compensating controls.
- Status: Open — Needs Human Decision (with Agent C)

### [INJ-12] Reviews go live immediately despite "eerst beoordeeld" promise (moderation bypass)
- Severity: Medium
- Category: Bug
- Location: `app/Http/Controllers/WebshopReviewController.php:35-44` (`'is_approved' => true`); UI text `product-details.blade.php:613` ("Je review wordt eerst beoordeeld…")
- Description: guest (and user) reviews are published + counted into the rating aggregate instantly (`recalcRating`), while the UI and the admin approve/reject flow (`ReviewController@approve/reject`) imply pre-moderation. No purchase verification either. Review bodies/titles ARE escaped at every render site (`products.js:216-228` uses `escapeHtml`; product page shows only aggregates) — so this is a trust/moderation issue, not XSS.
- Proof: POST valid review as guest → 201 with `is_approved=true` row + `rating_count` incremented, before any admin action. Validation itself is solid (rating 1–5, body 10–1000, throttle 5,1).
- Impact: review spam / rating manipulation without purchase.
- Not fixed — reason: flipping the default changes shop behavior (visible reviews disappear until approved); product decision.
- Regression test: none (behavior intentionally unchanged).
- Status: Open — Needs Human Decision

### [INJ-13] Tracking query uses MySQL-only CONCAT/LPAD (500s on SQLite; blocks test coverage)
- Severity: Low
- Category: Bug
- Location: `app/Http/Controllers/TrackingController.php:31-36`
- Description: `orWhereRaw("CONCAT('DR-', LPAD(id, 5, '0')) = ?")` throws `no such function: CONCAT` on SQLite (observed as 500s in the test run). Works on production MySQL, but means the tracking flow cannot be functionally tested on the project's SQLite suite, and any DB-portability change would break it in prod.
- Proof: `PDOException: SQLSTATE[HY000]: General error: 1 no such function: CONCAT` under `DB_CONNECTION=sqlite`.
- Impact: robustness/testability; no injection (value is bound; `where('id','like',"%$tNumber%")` is also bound — `%`/`_` act as wildcards only).
- Not fixed — reason: portable rewrite (compute `DR-` number in PHP, compare two bound columns) touches the lookup semantics; small but out of the safe-fix set for this pass.
- Regression test: throttle test deliberately uses invalid payloads to avoid depending on this query.
- Status: Open

### [INJ-14] Mollie webhook URL built from the request Host header
- Severity: Low
- Category: Config
- Location: `app/Http/Controllers/CheckoutController.php:212-221` (`publicWebhookUrl`: `$request->getHost()` + `getSchemeAndHttpHost`)
- Description: with default trust-proxy settings an attacker can poison `Host` at checkout, making the Mollie `webhookUrl` point at an attacker host. Fail-closed (Mollie can't reach it; order still confirmable via return URL; webhook authenticity itself is Agent E's domain), but worth hardening via `APP_URL` config instead of request host.
- Proof: code reads `$request->getHost()`; no `TrustHosts`/`trustProxies` configuration in `bootstrap/app.php`.
- Impact: low (webhook misdirection, no payment forgery by itself).
- Not fixed — reason: owned jointly by payments (Agent E) and config/proxy (Agent G) audits.
- Regression test: none.
- Status: Open — Needs Human Decision (with Agents E/G)

### [INJ-15] Residual data from pre-fix windows (needs one-off cleanup, human call)
- Severity: Medium (residual)
- Category: Quality
- Location: `products` table (`description`, `main_image`, `gallery_images`)
- Description: INJ-03/INJ-04 stop NEW bad rows; rows written before the fix keep raw `<script>`/SVG payloads and stay executable. Recommended one-off (human approval, backup first): artisan/console pass applying `HtmlSanitizer::productDescription()` to all product descriptions + report/delete `*.svg` under `products/*` on the public disk. Deliberately NOT done here (bulk data mutation).
- Proof: sanitizer applies only on store/update code path.
- Impact: until cleaned, historic stored-XSS/SVG rows remain live.
- Not fixed — reason: bulk mutation needs backup + owner sign-off.
- Regression test: n/a.
- Status: Open — Needs Human Decision

### [INJ-16] Dependency advisories (report only — Agent A owns composer.json)
- Severity: High (2) / Medium (1) / Low (3)
- Category: Config
- Location: `composer.lock` (no edits made)
- Description: `composer audit` reports 6 advisories / 4 packages: `league/commonmark` High (quadratic-DoS in GFM table scan; DoS via Attributes extension) + Medium (`DisallowedRawHtml` bypass) — pulled in by `laravel/framework`, not directly used by `app/` (no `use League\…` found; AI prompts explicitly forbid Markdown); `firebase/php-jwt` Low (weak encryption, CVE-2025-45769); `laravel/framework` Low (XSS in debug page, CVE-2026-102279 — production impact depends on `APP_DEBUG`, Agent G); `league/flysystem` Low (path-normalizer UTF-8 bypass, CVE-2026-102601). `npm audit --omit=dev`: **0 vulnerabilities**.
- Proof: `composer audit` output (6 advisories), `rg "use League" app/` empty, `npm audit` clean.
- Impact: upgrade tasks; no direct exploit path found in app code for the commonmark items.
- Not fixed — reason: explicitly owned by Agent A (do not edit composer.json/package.json).
- Regression test: none.
- Status: Open — handed to Agent A

### [INJ-17] Mail subject newline guard is single-point (info)
- Severity: Info
- Category: Quality
- Location: `app/Mail/CustomEmail.php:23-29`
- Description: `CustomEmail` takes `subjectText` verbatim into the envelope; only caller is the now-guarded `MailingListController@send`. Safe as long as no second caller passes unsanitized input. Noted for future callers.
- Status: Open — Info (no action unless new callers appear)

---

## Verified safe (no finding, evidence on file)

- **SQL injection**: all `whereRaw`/`selectRaw`/`orderByRaw` use parameter bindings (`WebshopController` JSON filters, `AdminController` subselect, `ChatProductSearch`, `Product::recalcRating`); zero dynamic `orderBy`/column names (all admin `data` endpoints order by fixed columns; `per_page` allowlisted; `sort` via `match()` allowlist in webshop); all LIKE values bound (unescaped `%`/`_` only widens matches — accepted). `scripts/*.php` SQL uses PDO + `(int)` casts + hardcoded table lists; CLI-only (`getopt`), not web-reachable (`public/` is docroot).
- **PageController@service LFI**: `slug → pageKey` strictly via `config('cms.service_slugs')` allowlist (404 otherwise); `view()->exists('landing.service-'.$pageKey)` only on allowlisted keys. Safe.
- **VideoStream**: `basename($file)` confined to `public/assets/video`; Range parsing bounds-checked (416 on invalid). Safe.
- **File serving**: `DownloadController` enforces admin / paid-owner / per-order signature + live `payment_status` recheck (payment details: Agent E); contact/chat/device/loan photo endpoints scoped (INJ-02 was the sole exception).
- **SSRF/outbound**: no server-side fetch of user-controlled URLs. `DuckDuckGoDriver`/`OpenAiClient` hit fixed hosts with validated params; `external_link`/`download_*_url` are `url`-validated and only rendered as links (escaped), never fetched.
- **Command injection**: no `exec/shell_exec/proc_open/passthru/Process` in `app/` or `routes/`; `mysqldump` only in CLI sync scripts with operator-supplied creds.
- **Mass assignment / validation**: every model uses `$fillable`; no `request()->all()` into persistence; prices/coupons server-computed (`CartService`: `price_snapshot` from `discounted_price`, qty 1–99 validated, totals server-side); `Coupon::discountAmount` capped at subtotal, percentage ≤100 validated, single coupon per cart, single-use checked per user/guest; checkout rechecks status/license stock, `shipping_method` allowlisted and forced `digital` for all-digital carts; technician quote/coupon math server-side (`TechnicianController@calculate`, validated `H:i` times). Coupon race/usage-counting at payment finalize: Agent E.
- **Mail headers**: all `Mail::to()` targets validated `email` rules; subjects code-controlled except INJ-07 (fixed).
- **Open redirects**: only `redirect()->away()` targets are Mollie SDK checkout URLs (https, provider-generated); all other redirects are `route()`/path based. None user-controlled.
- **Deserialization**: no `unserialize/eval/extract/include-with-variables` anywhere in `app/`.
- **DomPDF**: all invoice templates (`invoices/*.blade.php`) use escaped `{{ }}` exclusively.
- **CMS `{!! !!}` inventory (23 sites)**: all safe except INJ-04 — `nl2br(e(…))` pattern (emails/layout/customer/contact-reply, service views, footer), `legal-page` builds `$html` with `e()`, tracking icons + `service-afspraak` SVGs are hardcoded arrays, `primary-button` `$icon` never passed by any caller, `product-details` description was the vuln (fixed). AI chat widget escapes before linkify (`ai-chat.js:245,281,289-290,308-310` — user, admin and AI bodies all escaped; AI `html` is escaped-then-linkified, no raw-HTML render).
- **Admin JS innerHTML**: `escapeHtml()` used consistently across ~15 audited files (afspraak/reparatie/contact/chat-inbox/faqs/coupons/products/categories/orders/hardware/device-receipts/laptop-loans); single miss fixed (INJ-09). `coupons.js:40` error path interpolates server `message` strings only (hardcoded Dutch, no user data echoed).
- **Uploads**: contact/repair/chat/device/loan photos validate `image|mimes:…` + size, store uuid names on the non-public `local` disk, served via `response()->file/download` (no execution path; extension spoofing blocked by mime+extension agreement). CMS media restricted to `jpeg,png,webp,mp4,mov,webm` with `hashName()` into `public/assets/...`. Chunked upload: `upload_id` regex, integer index/total (now capped), ext allowlist (`zip,iso,exe,msi,pdf,dmg,pkg,7z,rar`), random 40-char final name under `digital/`, client `size` verified against assembled bytes, `name` reduced via `basename()`.
- **Afspraak/Contact/Repair requests**: strict FormRequests (allowlisted enums, `email` rules, `prohibited` honeypot incl. new INJ-08, `accepted` privacy, photo/image mimes + counts).

## CSRF matrix (exceptions in `bootstrap/app.php:34-44`)

| Route | Exempt | Compensating control | Verdict |
|---|---|---|---|
| `login/ register/ logout/` | yes | `@csrf` rendered, pages uncached | INJ-10 Open (should enforce; needs Agent F test updates) |
| `contact/submit` | yes | throttle 5,1 + honeypot | INJ-11 Open (needs Agent C: uncached tokens) |
| `track/` | yes | throttle 10,1 (INJ-06, new) | INJ-11 Open (same) |
| `ai-chat/*` | yes | per-endpoint throttle + 64-char capability token + honeypot | INJ-11 Open (same) |
| `payment/*webhook`, `lid-worden/webhook`, `technician/webhook` | yes | provider verification — Agent E | out of scope, flagged to E |
| everything else (incl. all admin POST/PUT/DELETE, cart, checkout, reviews, wishlist) | no | enforced; AJAX sends `X-CSRF-TOKEN` from meta (admin) | verified enforced |

Note: feature tests cannot assert 419s (framework skips CSRF under `runningUnitTests`); exemption state verified by code inspection + `@csrf` presence greps.

## Dependency audit

- `composer audit`: 6 advisories / 4 packages — see INJ-16 (handed to Agent A, no edits made).
- `npm audit --omit=dev`: 0 vulnerabilities.

## Files changed

- `app/Support/HtmlSanitizer.php` (new), `app/Support/SafeFilename.php` (new)
- `app/Services/InboundContactFetcher.php`, `app/Http/Controllers/Admin/RepairInboxController.php`, `app/Http/Controllers/Admin/Shop/FilesController.php`, `app/Http/Controllers/Admin/Shop/ProductController.php`, `app/Http/Controllers/Admin/MailingListController.php`, `app/Http/Requests/StoreAfspraakSubmissionRequest.php`, `public/assets/js/admin/products.js`, `tests/Feature/AuditInjectionTest.php` — commit `b79b8af`
- `routes/web.php` (track throttle — authored here, committed inside concurrent `0bc565f`)
- This report: `audit/04-injection.md` (commit: see log)

## Tests

`tests/Feature/AuditInjectionTest.php` — 11 tests, all green (SQLite `:memory:`):
sanitizer strip/unwrap (2), SafeFilename traversal battery, repair-photo whitelist/traversal, SVG rejection, description sanitization on store, chunk caps (3 asserts), track throttle (429), afspraak honeypot, mailing-subject newlines, admin-JS escape tripwire.
Neighbor run: `DigitalFilesTest` 8/8 green (no legit-flow breakage).
