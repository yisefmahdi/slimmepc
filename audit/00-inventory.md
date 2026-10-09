# SlimmePC Audit — 00 Inventory (branch `audit/full-review`, 2026-10-09)

Source of truth: `project-structure.md` (§1–§43, 1770 lines) + live code scan below.
Full route tables: `audit/routes.txt` (plain) and `audit/routes-verbose.txt` (with middleware).

## Counts

| Area | Count | Location |
|------|-------|----------|
| Routes (named + unnamed) | 259 | `routes/web.php`, `routes/admin.php`, `routes/auth.php` |
| Controllers | 56 files | `app/Http/Controllers/` (20 top-level + 9 Auth + 17 Admin + 3 Admin/Chat + 7 Admin/Shop) |
| Models | 43 | `app/Models/` |
| Migrations | 55 | `database/migrations/` (0001×3 … 2026_10_06 digital files) |
| Middleware (app) | 4 | `CheckIfBlocked`, `EnsureAdminOrTechnician`, `EnsureUserIsAdmin`, `SyncInboundReplies` |
| FormRequests | 12 | 9 top-level + `Auth/LoginRequest` + `Admin/StoreKlant + UpdateKlant` |
| Blade views | 180 | `resources/views/` |
| Frontend JS | 29 | `public/assets/js/` (incl. `admin/`, `vendor/`) |
| Test files | 31 | `tests/` |
| Config files | 13 | `config/` (app, auth, cache, cms, contact-inbox, database, filesystems, firebase, logging, mail, queue, services, session) |
| Sync scripts | 8 + verify | `scripts/sync-*.php`, `scripts/verify-sync.php` + `scripts/deploy.sh` |
| Mailables | ~20 | `app/Mail/` (see project-structure §24: 14 order/contact/repair + chat mails + technician/membership) |
| AI services | 8 | `app/Services/Ai/` + `app/Services/Chat/` + `CartService`, `MolliePaymentService`, `OrderPaymentService`, `AdminPushNotifier`, `ChatMailer` |

## Roles & auth model

- `users.role` enum: `admin / user / technician`. Helpers `isAdmin()/isTechnician()/isCustomer()`.
- Middleware aliases (verify in `bootstrap/app.php`): `admin` (=EnsureUserIsAdmin), `admin.or.tech`, `check.blocked`, `inbound.sync`.
- Admin panel: `/admin` prefix, `routes/admin.php`, own layout; login redirect role-based (`AuthenticatedSessionController`).
- `MustVerifyEmail` OFF on User model (per §24) — `verified` middleware still on admin group: verify whether it blocks anyone.

## Key subsystems (map to sub-agents)

- CMS: `content_blocks` + `content_meta`, `App\Support\Cms`, `config/cms.php`, `Admin\ContentController`, `PageController` (+ full-page HTML cache, guest-only).
- Webshop: categories/products/reviews/coupons/cart/checkout/favorites/search.
- Payments: `MolliePaymentService`, `OrderPaymentService::finalizeOrder`, `PaymentController@webhook/return`, technician + membership finalize paths.
- Digital goods (NEW, §44/§47 in prompt): `LicenseCode`, `DigitalFile`, `DigitalDownload`, `Admin\Shop\LicenseCodeController`, `FilesController` (chunked 4MB upload), `DownloadController GET /download/bestand/{file}` (admin / paid-owner / HMAC).
- AI: `Admin\Shop\AiProductController`, `AiService`, `OpenAiClient`, `DuckDuckGoDriver`, `ChatAnswerGenerator`, embeddings.
- Inboxes: contact, repair (`repair_submissions`), afspraak (`afspraak_submissions`), chat (`chat_*`), orders, device receipts + photos, manual invoices (`SLM-`), memberships, technician forms/invoices (`SLP-`), laptop loans, mass email + mailing_list (244 real emails).
- Tracking: `TrackingController` (`/track`, CSRF-exempt), `VideoStreamController` (Range), `SitemapController`.
- Push: `kreait/firebase-php`, `FcmToken`, `AdminPushLog`, `AdminPushNotifier`, `PushController`, SW route `/firebase-messaging-sw.js`.

## Immediate red flags (to verify, NOT yet confirmed)

1. `h_00094667_slimmepc.sql` (649KB legacy DB dump, real customer PII) sits in repo root, **untracked** — must never be committed; check `.gitignore`.
2. `scripts/backups/` (mysqldump per sync run) **untracked** — check `.gitignore` + contents.
3. `scripts/sync-*.php` take DB creds; confirm CLI-only, no hardcoded secrets, and whether `--config=db.json` files linger in repo.
4. CSRF-exempt list in `bootstrap/app.php` reportedly includes `contact/submit`, `track/`, `ai-chat/*`, payment webhooks, lidmaatschap/technician webhooks — each needs alternate auth review.
5. Admin group middleware per verbose list starts with `admin.or.tech` on `/admin` dashboard itself — confirm technicians cannot reach admin-only controllers (verify per-controller, not just route group).
6. Seeded admin `slimmepc@admin.com` with documented password in project-structure.md — confirm seeder password handling + production rotation.
7. `APP_DEBUG`, session/cookie flags, security headers, `.htaccess`, storage symlink exposure — all open (Sub-agent G).
8. Full-page HTML cache guest-only claim + CSRF token caching risk (Sub-agent C).
9. `{!! !!}` usages, TinyMCE HTML, DomPDF, SVG uploads, chunk-upload path traversal (Sub-agent D).
10. Mollie webhook trust, amount check, idempotency, licence atomicity (Sub-agent E).

## Environment (local audit box)

- PHP 8.2.8 CLI, Node v24.21.0 (doc says v18 — upgraded since), MySQL `slimmepc_2026` in `.env` (DO NOT migrate:fresh against it; use test DB).
- Branch: `audit/full-review` (based on `main` @ `49ef182` + dirty: M project-structure.md, M scripts/sync-phase*.php, untracked dump/backups/sync scripts).
