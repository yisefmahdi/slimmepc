# SlimmePC Audit — 02 Authentication & Authorization (Sub-agent B)

Branch: `audit/full-review` · Date: 2026-10-09 · Auditor: Sub-agent B (AuthN/AuthZ)
Scope: route→middleware map (264 routes, source `audit/routes-verbose.txt`), IDOR/BOLA,
privilege escalation, login/registration, `verified` enforcement, blocked-user sessions.
Tests: `tests/Feature/AuditAuthTest.php` (21 tests, SQLite :memory:).

> **Shared-checkout note (important for attribution).** This branch is edited concurrently by
> other audit agents, and uncommitted files are swept into shared commits. Commit `0bc565f`
> (`audit(pay)`, 2026-10-09) contains BOTH the payment agent's hardening AND the following hunks
> authored by Sub-agent B (this report): `TechnicianController::loginSubmit` (generic errors + session
> regenerate), `requireTechnician()` + its four call-site gates, `LidmaatschapController::store`
> klantnummer explicit assignment, and the `CheckoutController`/`PaymentController` reconciliation
> (dead-code removal). Route-level `auth` on technician routes, `amountMatches`, `mayViewOrder` /
> `owned_orders`, `mayViewMembership`, `mayViewForm`, `track` throttle and the AI-endpoint throttle are the
> sibling's work. My own commit (pending at report time) carries: `User.php`, `Admin\KlantController.php`,
> `ImportUsersCommand.php`, `AdminUserSeeder.php`, `routes/auth.php`, `ContactSubmitTest.php`,
> `tests/Feature/AuditAuthTest.php`, and this file. "Fix applied (my commit)" below means that pending
> commit; "committed via 0bc565f" means already in HEAD through the shared sweep.

## Result summary

| Severity | Found | Fixed | Partially fixed | Open |
|----------|-------|-------|-----------------|------|
| Critical | 0 | 0 | 0 | 0 |
| High | 6 | 5 | 1 | 0 |
| Medium | 4 | 3 | 0 | 1 |
| Low | 4 | 0 | 0 | 4 |
| Info | 3 | 0 | 0 | 3 (documented, incl. checked-OK) |

Fixed (my authorship; commit split by the shared sweep — see note): AUTH-01, AUTH-02 (role layer;
route layer by sibling), AUTH-07, AUTH-08, AUTH-09.
Fixed by sibling, regression-covered by me (all committed via `0bc565f`): AUTH-03, AUTH-04, AUTH-05.
Partially fixed: AUTH-10 (env override; prod rotation still required).
Open / Needs Human Decision: AUTH-06, AUTH-11, AUTH-14 (+ AUTH-10 rotation).
Open / accepted residual (Low): AUTH-12, AUTH-13. Info: AUTH-15, AUTH-16, §8 checked-OK list.

---

### [AUTH-01] User $fillable allowed mass assignment of role / is_blocked / klantnummer
- Severity: High
- Category: AuthZ
- Location: `app/Models/User.php:21` (was: `role`, `is_blocked`, `klantnummer`, `email_verified_at` fillable)
- Description: `User::$fillable` contained `role`, `is_blocked`, `klantnummer` (and `email_verified_at`).
  Any current or future `User::create($requestData)` / `->update($requestData)` / `->fill()` sink with
  attacker-controlled input = instant privilege escalation (set `role=admin`), account unblocking,
  or klantnummer spoofing. Audited all sinks: `RegisteredUserController::store` (explicit field list — safe),
  `ProfileController::update` via `ProfileUpdateRequest` (only `name`/`email` validated — safe),
  `Admin\KlantController` (admin-only FormRequests with `authorize()->isAdmin()` — intended), so no
  remotely exploitable sink exists today; the finding is latent but load-bearing (classic mass-assignment).
- Proof: `User::create(['email'=>..., 'role'=>'admin', ...])` produced an admin before the fix
  (verified in tinker-style script against sqlite; DB default `user` was bypassed by the supplied key).
- Impact: privilege escalation to admin on any future/refactored sink; silent role changes.
- Fix applied: narrowed `$fillable` to `name, phone, house_number, street, postcode, city, email,
  email_verified_at, password`. Privileged fields are now assigned explicitly:
  `Admin\KlantController::store/update/toggleBlock/updateRole` (`app/Http/Controllers/Admin/KlantController.php`,
  my commit), `LidmaatschapController::store` klantnummer (`app/Http/Controllers/LidmaatschapController.php:70`,
  committed via `0bc565f`), `ImportUsersCommand` (firstOrNew + fill + forceFill, my commit),
  `AdminUserSeeder` (firstOrNew + fill + forceFill, my commit).
  `email_verified_at` was deliberately KEPT fillable: the suite-wide factory verified-default depends on it
  and no request path assigns it (verification writes use `forceFill` in `markEmailAsVerified`).
  `tests/Feature/ContactSubmitTest.php` (8× `User::factory()->create(['role'=>'admin'])`) updated to
  explicit assignment since factories respect `$fillable`.
- Regression test: `tests/Feature/AuditAuthTest.php` — "ignores privileged fields on mass assignment",
  "does not escalate role via profile update", "does not escalate role via registration",
  "still lets admins manage roles through the admin controller".
- Status: Fixed

### [AUTH-02] Technician payment flow (PII + invoice creation) reachable without login
- Severity: High
- Category: AuthZ
- Location: `app/Http/Controllers/TechnicianController.php:paymentPage,storePaymentForm,quote,checkCoupon`
  Routes (snapshot): `GET /technician/payment/{klantnummer}`, `POST /technician/payment/submit`,
  `POST /technician/quote`, `POST /technician/check-coupon` (all PUBLIC, throttle-only).
- Description: `paymentPage()` did `User::where('klantnummer', ...)->firstOrFail()` with zero auth —
  anyone enumerating `SLP-######` numbers could read client PII (name/address/membership pricing) and,
  via `storePaymentForm()`, create payable `TechnicianForm` + Mollie invoices for arbitrary clients
  (`technician_id = Auth::id()` = null for guests). `quote`/`checkCoupon` disclosed per-client pricing,
  membership status and coupon validity anonymously.
- Proof: `GET /technician/payment/SLP-123456` as guest → 200 with client data (before fix).
  `POST /technician/payment/submit` as guest → Mollie redirect / form row created (before fix).
- Impact: PII harvesting (klantnummer→name/address), invoice-creation abuse, coupon oracle.
- Fix applied (two layers): (1) sibling added route-level `auth` middleware in `routes/web.php:102-105`
  (committed via `0bc565f`); (2) MY controller role gate `requireTechnician()` on all four
  methods — only `technician`/`admin` roles, non-blocked; guests→redirect `technician.login`, JSON→403
  (my hunks, committed via `0bc565f` sweep; behaviour pinned by my tests below).
  Layered result verified by tests: guest→`/login` (route), authed user→`/betaal/login` (gate), tech/admin→200.
- Regression test: "redirects guests away from the technician payment page", "blocks guests from creating
  technician payment forms", "blocks guests from the technician quote and coupon endpoints",
  "blocks regular users from the technician payment page", "lets a logged-in technician open the payment page".
- Status: Fixed

### [AUTH-03] Public order result pages enumerated orders (items + totals + customer e-mail)
- Severity: High
- Category: AuthZ
- Location: `app/Http/Controllers/PaymentController.php:return,success,failed`
  Routes: `GET /payment/return/{order}`, `GET /payment/success?order=N`, `GET /payment/failed?order=N` (PUBLIC).
- Description: sequential integer order IDs with no ownership/proof check. `payment-success` blade prints
  items, totals AND `customer_email` ("Je ontvangt de factuur per e-mail op …"). Anyone iterating `?order=N`
  harvested order contents + e-mails.
- Proof (before fix): create paid order for user A; as user B `GET /payment/success?order=<id>` → 200
  containing A's e-mail and items.
- Impact: customer PII + order disclosure at scale.
- Fix applied: NOT MY CODE — sibling's `mayViewOrder()` (owner / admin / `owned_orders` session flag set in
  `CheckoutController::store` for guest orders) + paid-only success, present in working tree. I removed my own
  interim duplicate (`canSeeOrder`/`successView`/`order_access`) to keep a single mechanism.
- Regression test: (mine) "hides other peoples orders on the public payment success page",
  "hides other peoples orders on the payment return page".
- Status: Fixed

### [AUTH-04] Membership success page enumerated members (name + e-mail + klantnummer)
- Severity: High
- Category: AuthZ
- Location: `app/Http/Controllers/LidmaatschapController.php:success`
  Route: `GET /lid-worden/success/{lidmaatschap}` (PUBLIC, sequential IDs).
- Description: blade prints member name, klantnummer, e-mail, dates, total. No proof-of-ownership.
- Proof (before fix): paid membership id N; stranger `GET /lid-worden/success/N` → 200 with name+e-mail.
- Impact: member PII harvesting.
- Fix applied: NOT MY CODE — sibling's `mayViewMembership()` (paid + owner/admin/payer-session flag set in
  `mollieReturn`), present in working tree. My only hunk in this file is the AUTH-01 klantnummer assignment.
- Regression test: (mine) "blocks strangers from other peoples membership success pages".
- Status: Fixed

### [AUTH-05] Technician success page enumerated paid forms (invoice no + total)
- Severity: High (downgraded from Critical: no names/e-mails exposed)
- Category: AuthZ
- Location: `app/Http/Controllers/TechnicianController.php:success`
  Route: `GET /technician/success/{form}` (PUBLIC, sequential IDs; only `abort_unless(paid)`).
- Description: sequential form IDs; page shows invoice number + amount paid. Lower sensitivity than AUTH-03/04
  (no names/e-mails — invoice numbers are random per `TechnicianInvoice`, sibling hardened), but still a
  payment oracle (which form IDs are paid + amounts).
- Fix applied: NOT MY CODE — sibling's `mayViewForm()` (admin/technician always; others need payer-session
  flag set in `mollieReturn`), present in working tree.
- Regression test: (mine) "blocks strangers from other peoples technician success pages".
- Status: Fixed

### [AUTH-06] `verified` middleware on the admin group is a silent no-op
- Severity: Info
- Category: AuthN
- Location: `routes/admin.php:26` (`->middleware(['auth', 'verified', ...])`), `app/Models/User.php`
- Description: `User` uses the `MustVerifyEmail` *trait* (methods exist) but does NOT implement the
  `Illuminate\Contracts\Auth\MustVerifyEmail` *interface* (verified via reflection: NOT-IMPLEMENTING).
  Laravel's `EnsureEmailIsVerified` only acts on `instanceof MustVerifyEmail`, so `verified` passes every
  user through — unverified accounts reach the entire admin panel. The middleware suggests an assurance
  that does not exist.
- Proof: unverified admin factory user `GET /admin` → 200 (regression test below pins this real effect).
- Impact: none today (no verification-gated business logic), but misleading; any future reliance on
  `verified` would be ineffective.
- Fix applied: none — enabling real enforcement (`implements MustVerifyEmail`) would lock out all legacy/
  synced unverified users and change login/registration UX. Needs Human Decision.
- Regression test: "does not block unverified users via the verified middleware" (documents current effect).
- Status: Open — Needs Human Decision (either implement interface + rollout plan, or drop the middleware
  to stop implying protection).

### [AUTH-07] No rate limiting on register / forgot-password / reset-password / confirm-password POST
- Severity: Medium
- Category: AuthN
- Location: `routes/auth.php` (before fix: no throttle on any of these POSTs)
- Description: `POST /register` allowed unbounded account creation (+ admin notification e-mail on
  `?nieuwe-klant` = mail-bomb vector). `POST /forgot-password` allowed unbounded reset e-mails (password-reset
  broker has a per-address 60s throttle, but no per-IP guard). `POST /reset-password` token endpoint and
  authed `POST /confirm-password` (password guessing with a stolen session) had no throttle.
  (`POST /login` was already protected inside `LoginRequest` — 5 attempts per e-mail+IP with lockout event.)
- Impact: account-spam, e-mail bombing, token/session-password brute force facilitation.
- Fix applied (my commit): `routes/auth.php` — register `throttle:10,1`, forgot-password `throttle:5,1`,
  reset-password `throttle:10,1`, confirm-password `throttle:10,1`.
- Regression test: "throttles mass registration attempts" (11th POST → 429). Existing Auth/* tests unaffected
  (40 tests incl. Auth dir pass).
- Status: Fixed

### [AUTH-08] Session fixation in technician login (`betaal/login`)
- Severity: Medium
- Category: AuthN
- Location: `app/Http/Controllers/TechnicianController.php:loginSubmit` (was: `Auth::attempt` with no
  `$request->session()->regenerate()`)
- Description: after successful technician authentication the pre-login session ID was kept, so a fixated
  session ID (e.g. planted via link) survived the privilege change. The main login
  (`AuthenticatedSessionController::store`) regenerates correctly — this was inconsistent.
- Impact: session-fixation account takeover for technician accounts.
- Fix applied: `$request->session()->regenerate()` after all checks pass, before redirect
  (my hunk, committed via `0bc565f`).
- Regression test: "regenerates the session on technician login" (session ID changes across the POST).
- Status: Fixed

### [AUTH-09] Technician login errors enumerated accounts, roles and klantnummers
- Severity: Medium
- Category: AuthN
- Location: `app/Http/Controllers/TechnicianController.php:loginSubmit` (was: three distinct messages —
  "Verkeerde inloggegevens." vs "Je hebt geen toegang tot deze pagina." vs "Klantnummer staat niet op
  ons data."; blocked users got the distinctive `CheckIfBlocked::MESSAGE`)
- Description: distinct errors let attackers separate (a) valid technician e-mail+password combos,
  (b) non-technician accounts, (c) valid klantnummers, (d) blocked accounts.
- Impact: credential/klantnummer enumeration oracle (also feeds AUTH-02 style targeting).
- Fix applied: single generic message "Verkeerde inloggegevens." for every failure path
  (my hunk, committed via `0bc565f`).
- Regression test: "returns a generic error for every technician login failure",
  "refuses login for blocked technicians without revealing the block".
- Status: Fixed

### [AUTH-10] Seeded admin uses a weak, publicly documented password
- Severity: High
- Category: AuthN
- Location: `database/seeders/AdminUserSeeder.php` (`slimmepc@admin.com` / `slimmepc@@#@10`),
  password also documented in `project-structure.md`
- Description: deterministic well-known admin credentials. If production was ever seeded (or re-seeded)
  with this password and not rotated, the admin panel is wide open. Seeder uses `updateOrCreate`-equivalent
  semantics, so re-running re-applies the known password.
- Impact: full admin compromise if prod still carries the seeded secret.
- Fix applied (partial, my commit): seeder rewritten to `firstOrNew` + `fill` + `forceFill` (AUTH-01
  compatible) with password from `env('SEED_ADMIN_PASSWORD')`, falling back to the previous default for
  local dev only.
- Regression test: none (secret handling; covered by code review).
- Status: Partially fixed — Needs Human Decision: (1) rotate the production admin password NOW if the seed
  ever ran there, (2) set `SEED_ADMIN_PASSWORD` via server env for future seeds, (3) remove the plaintext
  password from `project-structure.md`/docs.

### [AUTH-11] Technicians can read/write all device receipts (customer PII) via ontvangst routes
- Severity: Medium
- Category: AuthZ
- Location: `routes/admin.php:190-202` (`admin/bevestiging-mail/ontvangst/*` group has NO `admin` middleware;
  only `destroy`/`photo.destroy` do). Controller: `app/Http/Controllers/Admin/DeviceReceiptController.php`
  (index/data/create/store/show/status/photo reachable with `admin.or.tech` only)
- Description: any technician account lists/searches (`data` supports free-text search over name/e-mail/
  serial/phone), views (`show` + `photoUrls`), creates and status-changes every device receipt, including
  devices handed in by other customers. Technicians also see the `/admin` dashboard aggregates. The group
  comment claims "fine-grained protection per-route", but read access to the full customer+PII set is
  tech-wide, not scoped (e.g. to own intake).
- Proof: route table rows 16–24 (this file, §9): 7 of 9 ontvangst routes lack `AD`.
- Impact: bulk customer PII (names, e-mails, phone numbers, serials, device photos) visible to every
  technician account; integrity (status changes, fake receipts) tech-wide.
- Fix applied: none — whether technicians NEED intake access (field workflow) is a business decision.
  Needs Human Decision: either add `->middleware('admin')` to the read/create/status routes (keep
  photo/destroy admin-only as now), or scope `data`/`show` to the technician's own receipts and document it.
- Regression test: none (behaviour intentionally unchanged).
- Status: Open — Needs Human Decision

### [AUTH-12] Membership/technician *failed* pages disclose klantnummers by sequential ID
- Severity: Low
- Category: Data Leak
- Location: `LidmaatschapController::failed` (`GET /lid-worden/failed/{id}`),
  `TechnicianController::failed` (`GET /technician/failed/{form}`) — both PUBLIC, no proof check.
  Blades print `$lidmaatschap->klantnummer` / `$form->user->klantnummer`.
- Description: iterating IDs confirms klantnummer↔record linkage. Post AUTH-02-fashion hardening a bare
  klantnummer is nearly useless (payment pages need operator auth; `paymentPage`/`quote`/`checkCoupon` gated),
  and no names/e-mails/totals leak here — hence Low, not High like the success pages.
- Fix applied: none — proper fix is unguessable result URLs (signed tokens in the Mollie `redirectUrl`),
  which touches payment redirect generation (sibling payment domain) and cross-device UX. Flagged, not silently changed.
- Regression test: none (behaviour intentionally unchanged).
- Status: Open

### [AUTH-13] User enumeration via register / password-reset responses (framework-standard)
- Severity: Low
- Category: AuthN
- Location: `RegisteredUserController::store` (`email.unique` message), `PasswordResetLinkController::store`
  (distinct `__($status)` for unknown e-mail)
- Description: `POST /register` with a taken e-mail returns "Dit e-mailadres is al in gebruik." (well, the
  default unique message); `POST /forgot-password` returns different text for unknown vs known addresses.
  Allows e-mail existence checks. Standard Laravel Breeze behaviour; login itself is safe
  (`trans('auth.failed')` generic + LoginRequest lockout).
- Fix applied: none (unifying these messages changes UX copy and error-bag expectations; team call).
- Status: Open (accepted residual; revisit with product).

### [AUTH-14] CSRF exemption for login / register / logout (login-CSRF)
- Severity: Low
- Category: CSRF
- Location: `bootstrap/app.php:34-44` (`validateCsrfTokens(except: [... 'login/', 'register/', 'logout/', ...])`)
- Description: state-changing auth endpoints skip CSRF validation. Consequence is login-CSRF (attacker logs
  the victim into the attacker's account to observe entered data) rather than classic CSRF; `logout/` exempt
  enables logout-CSRF (nuisance). The exemption likely exists because of full-page HTML caching of CSRF
  tokens (Agent C/G domain) — removing it without fixing token freshness would 419 legit logins.
- Fix applied: none — cross-cutting with caching; Needs Human Decision with Agent C/G.
- Status: Open — Needs Human Decision

### [AUTH-15] Digital-download signed URLs never expire (accepted design, verified safe access control)
- Severity: Info
- Category: AuthZ
- Location: `app/Models/DigitalFile.php:signedUrlForOrder` (`URL::signedRoute` without expiry),
  `app/Http/Controllers/DownloadController.php:file`
- Description: per-order links are indefinite bearer tokens (forwardable). Mitigations verified in code:
  signature required when `?order=` present; `payment_status=paid` re-checked live on every hit
  (refunds/cancels revoke instantly); logged-in path requires a paid order containing the file; admins
  bypass intentionally; per-hit `DigitalDownload` audit rows + `downloads_count`. `/storage/{path}` serve
  routes require valid signatures (private disk) — verified in framework `ServeFile`/`ReceiveFile`.
  Expiry would break emailed guest links (no account to re-issue); changing needs product decision.
- Status: Open (documented; no change).

### [AUTH-16] Guest product reviews are published immediately (`is_approved=true`)
- Severity: Low
- Category: Quality
- Location: `app/Http/Controllers/WebshopReviewController.php:43`
  Route: `POST /webshop/{categorySlug}/{productSlug}/reviews` (PUBLIC, `throttle:5,1`)
- Description: anyone (no account, no purchase check) can publish a review that goes live instantly despite
  the "wordt binnenkort beoordeeld" copy. Spam/defacement vector; stored-XSS impact depends on blade
  escaping (Agent D domain). Throttle slows but does not stop it.
- Fix applied: none (moderation workflow = product decision; suggest default `is_approved=false` + admin
  approve queue, which already exists: `admin/webshop/reviews/*`).
- Status: Open

## §8 Checked explicitly, no issue found (AuthZ/AuthN)

- `GET /mijn-bestellingen*` (3 routes, `auth`): index/show/invoice all scoped
  `where('user_id', auth()->id())` (+ random `ORD-XXXXXXXX` order numbers); cross-user → 404 (covered by
  pre-existing `AccountOrdersTest`, still passing).
- Wishlist (`auth`): `toggle` forces `user_id` from session; `destroy` aborts 403 on owner mismatch
  (pre-existing `WishlistTest` passes; my suite re-asserts the boundary intent).
- Cart item routes (`PATCH/DELETE /cart/items/{item}`): scoped via `resolveCart($request)->items()->where('id', …)`
  — no cross-cart IDOR (`CartController:124-125,150-151`).
- Checkout `saved_address_id`: re-scoped to owner (`CheckoutController:111-114`); guest addresses carry `user_id=null`.
- Admin push tokens (`admin/notificaties/tokens*`, admin-only): `store` binds `user_id=auth()->id()` via
  `updateOrCreate(['token'=>…])` (token-steal can reassign — self-service semantics, acceptable);
  `destroy`/`test` strictly own-devices (`PushController:57-64,73`).
- Admin role machinery: `updateRole`/`toggleBlock`/`destroy` all refuse self-targeting; `toggleBlock` refuses
  admins; `StoreKlantRequest`/`UpdateKlantRequest::authorize()` double-checks `isAdmin()` on top of route middleware.
- AI endpoints (`ai-chat/*`, PUBLIC by design): capability URLs use 64-char `guest_token`; `photo/{message}`
  re-resolves the token and 404s on mismatch; per-endpoint throttles present; `history` only for authed users
  and returns their own rows (incl. tokens — acceptable, own data).
- Licence/chunk/AI-admin endpoints (`admin/webshop/license-codes/*`, `bestanden/chunk|complete`,
  `products/generate-description`): all under `admin` middleware. Chunk upload is admin-only.
- `MustVerifyEmail` trait methods exist (verification mails/links function — `EmailVerificationTest` passes);
  only the *middleware enforcement* is absent (AUTH-06).
- Remember-me (`LoginRequest` passes `remember` flag), session regeneration on login/logout, `intended()`
  redirects (internal URLs only — no open redirect), role-based post-login redirect (admin→dashboard) all reviewed OK.
- Blocked users: `CheckIfBlocked` runs on every `web` request (global append) → instant logout + session
  invalidation; `LoginRequest::authenticate` and technician `loginSubmit` both refuse blocked logins;
  deleted users resolve to `null` via the session guard → `auth` middleware bounces them. Remember-me cookies
  of blocked users die on first use. (Regression-tested.)
- `GET /storage/{path}` + `PUT`: framework-signed (relative signature required on the private `local` disk),
  traversal-safe (`PathTraversalDetected` → 404). Not an open file store. `local` disk has `serve=true` but
  invoice/chat/receipt files are only reachable via signed URLs or the owning controllers.
- `GET /stream/video/{file}`: `basename()`-constrained to `public/assets/video` (public assets only).
- Mollie/technician/membership webhooks: signature-of-truth is server-side `getPayment()` + (sibling-added)
  `amountMatches()`; no state change trusted from POST body alone. Payment-amount logic itself = Agent E domain.

## §9 Full route → middleware → who-can-reach-it table (264 routes)

Source: `audit/routes-verbose.txt` (generated 2026-10-09, BEFORE this audit's route changes).
Legend: `A`=auth, `G`=guest, `V*`=`verified` (NO-OP, see AUTH-06), `B`=`check.blocked` (also global on `web`),
`AT`=`admin.or.tech`, `AD`=`admin`, `IS`=`inbound.sync`, `S`=signed, `T`=throttle `x,y`.

Route changes applied AFTER the snapshot: `auth` added to the four technician operator routes
(`routes/web.php:102-105`, via `0bc565f`); `throttle:10,1` added to `POST /track` (via `0bc565f`);
`throttle:10,1` added to `POST /admin/webshop/products/generate-description` (`routes/admin.php`, via `0bc565f`);
`throttle:10,1` / `5,1` / `10,1` / `10,1` added to
`POST /register`, `/forgot-password`, `/reset-password`, `/confirm-password` respectively
(`routes/auth.php`, my commit).
`/storage/{path}` rows show no route middleware but require a valid URL signature (see AUTH-15/§8).

| # | Methods | Route | Middleware | Who can reach it |
|---|---------|-------|------------|------------------|
| 1 | GET|HEAD | `/` | — | PUBLIC |
| 2 | GET|HEAD | `/admin` | A V* B AT IS | admin + technician |
| 3 | GET|HEAD | `/admin/afspraak-aanvragen` | A V* B AT IS AD | admin only |
| 4 | GET|HEAD | `/admin/afspraak-aanvragen/data` | A V* B AT IS AD | admin only |
| 5 | GET|HEAD | `/admin/afspraak-aanvragen/new-count` | A V* B AT IS AD | admin only |
| 6 | GET|HEAD | `/admin/afspraak-aanvragen/{afspraakSubmission}` | A V* B AT IS AD | admin only |
| 7 | DELETE | `/admin/afspraak-aanvragen/{afspraakSubmission}` | A V* B AT IS AD | admin only |
| 8 | POST | `/admin/afspraak-aanvragen/{afspraakSubmission}/status` | A V* B AT IS AD | admin only |
| 9 | GET|HEAD | `/admin/bevestiging-mail/hardware` | A V* B AT IS AD | admin only |
| 10 | POST | `/admin/bevestiging-mail/hardware` | A V* B AT IS AD | admin only |
| 11 | GET|HEAD | `/admin/bevestiging-mail/hardware/create` | A V* B AT IS AD | admin only |
| 12 | GET|HEAD | `/admin/bevestiging-mail/hardware/data` | A V* B AT IS AD | admin only |
| 13 | DELETE | `/admin/bevestiging-mail/hardware/{invoice}` | A V* B AT IS AD | admin only |
| 14 | GET|HEAD | `/admin/bevestiging-mail/hardware/{invoice}/download` | A V* B AT IS AD | admin only |
| 15 | GET|HEAD | `/admin/bevestiging-mail/hardware/{invoice}/preview` | A V* B AT IS AD | admin only |
| 16 | GET|HEAD | `/admin/bevestiging-mail/ontvangst` | A V* B AT IS | admin + technician |
| 17 | POST | `/admin/bevestiging-mail/ontvangst` | A V* B AT IS | admin + technician |
| 18 | GET|HEAD | `/admin/bevestiging-mail/ontvangst/create` | A V* B AT IS | admin + technician |
| 19 | GET|HEAD | `/admin/bevestiging-mail/ontvangst/data` | A V* B AT IS | admin + technician |
| 20 | GET|HEAD | `/admin/bevestiging-mail/ontvangst/{receipt}` | A V* B AT IS | admin + technician |
| 21 | DELETE | `/admin/bevestiging-mail/ontvangst/{receipt}` | A V* B AT IS AD | admin only |
| 22 | GET|HEAD | `/admin/bevestiging-mail/ontvangst/{receipt}/photo/{photo}` | A V* B AT IS | admin + technician |
| 23 | DELETE | `/admin/bevestiging-mail/ontvangst/{receipt}/photo/{photo}` | A V* B AT IS AD | admin only |
| 24 | POST | `/admin/bevestiging-mail/ontvangst/{receipt}/status` | A V* B AT IS | admin + technician |
| 25 | ANY | `/admin/boekhouden` | A V* B AT IS AD | admin only |
| 26 | GET|HEAD | `/admin/chat/beschikbaarheid` | A V* B AT IS AD | admin only |
| 27 | POST | `/admin/chat/beschikbaarheid/vrije-dagen` | A V* B AT IS AD | admin only |
| 28 | DELETE | `/admin/chat/beschikbaarheid/vrije-dagen/{closedDate}` | A V* B AT IS AD | admin only |
| 29 | PUT | `/admin/chat/beschikbaarheid/{availability}` | A V* B AT IS AD | admin only |
| 30 | GET|HEAD | `/admin/chat/faqs` | A V* B AT IS AD | admin only |
| 31 | POST | `/admin/chat/faqs` | A V* B AT IS AD | admin only |
| 32 | GET|HEAD | `/admin/chat/faqs/data` | A V* B AT IS AD | admin only |
| 33 | GET|HEAD | `/admin/chat/faqs/{faq}` | A V* B AT IS AD | admin only |
| 34 | PUT | `/admin/chat/faqs/{faq}` | A V* B AT IS AD | admin only |
| 35 | DELETE | `/admin/chat/faqs/{faq}` | A V* B AT IS AD | admin only |
| 36 | POST | `/admin/chat/faqs/{faq}/toggle` | A V* B AT IS AD | admin only |
| 37 | GET|HEAD | `/admin/chat/inbox` | A V* B AT IS AD | admin only |
| 38 | GET|HEAD | `/admin/chat/inbox/data` | A V* B AT IS AD | admin only |
| 39 | GET|HEAD | `/admin/chat/inbox/new-count` | A V* B AT IS AD | admin only |
| 40 | GET|HEAD | `/admin/chat/inbox/photo/{chatMessage}` | A V* B AT IS AD | admin only |
| 41 | POST | `/admin/chat/inbox/sync` | A V* B AT IS AD | admin only |
| 42 | GET|HEAD | `/admin/chat/inbox/{conversation}` | A V* B AT IS AD | admin only |
| 43 | DELETE | `/admin/chat/inbox/{conversation}` | A V* B AT IS AD | admin only |
| 44 | POST | `/admin/chat/inbox/{conversation}/reply` | A V* B AT IS AD | admin only |
| 45 | POST | `/admin/chat/inbox/{conversation}/status` | A V* B AT IS AD | admin only |
| 46 | POST | `/admin/chat/inbox/{conversation}/toggle-ai` | A V* B AT IS AD | admin only |
| 47 | GET|HEAD | `/admin/contact-inbox` | A V* B AT IS AD | admin only |
| 48 | GET|HEAD | `/admin/contact-inbox/data` | A V* B AT IS AD | admin only |
| 49 | GET|HEAD | `/admin/contact-inbox/new-count` | A V* B AT IS AD | admin only |
| 50 | GET|HEAD | `/admin/contact-inbox/reply/{contactReply}/attachment` | A V* B AT IS AD | admin only |
| 51 | POST | `/admin/contact-inbox/sync` | A V* B AT IS AD | admin only |
| 52 | GET|HEAD | `/admin/contact-inbox/{contactSubmission}` | A V* B AT IS AD | admin only |
| 53 | DELETE | `/admin/contact-inbox/{contactSubmission}` | A V* B AT IS AD | admin only |
| 54 | GET|HEAD | `/admin/contact-inbox/{contactSubmission}/attachment` | A V* B AT IS AD | admin only |
| 55 | POST | `/admin/contact-inbox/{contactSubmission}/reply` | A V* B AT IS AD | admin only |
| 56 | POST | `/admin/contact-inbox/{contactSubmission}/status` | A V* B AT IS AD | admin only |
| 57 | GET|HEAD | `/admin/content` | A V* B AT IS AD | admin only |
| 58 | GET|HEAD | `/admin/content/design` | A V* B AT IS AD | admin only |
| 59 | POST | `/admin/content/design` | A V* B AT IS AD | admin only |
| 60 | POST | `/admin/content/media` | A V* B AT IS AD | admin only |
| 61 | GET|HEAD | `/admin/content/{page}/section/{section}` | A V* B AT IS AD | admin only |
| 62 | POST | `/admin/content/{page}/section/{section}` | A V* B AT IS AD | admin only |
| 63 | GET|HEAD | `/admin/dashboard` | A V* B AT IS | admin + technician |
| 64 | GET|HEAD | `/admin/email-verzenden` | A V* B AT IS AD | admin only |
| 65 | POST | `/admin/email-verzenden` | A V* B AT IS AD | admin only |
| 66 | GET|HEAD | `/admin/leen-huur` | A V* B AT IS AD | admin only |
| 67 | POST | `/admin/leen-huur` | A V* B AT IS AD | admin only |
| 68 | GET|HEAD | `/admin/leen-huur/create` | A V* B AT IS AD | admin only |
| 69 | GET|HEAD | `/admin/leen-huur/data` | A V* B AT IS AD | admin only |
| 70 | PUT | `/admin/leen-huur/{loan}` | A V* B AT IS AD | admin only |
| 71 | GET|HEAD | `/admin/leen-huur/{loan}` | A V* B AT IS AD | admin only |
| 72 | DELETE | `/admin/leen-huur/{loan}` | A V* B AT IS AD | admin only |
| 73 | GET|HEAD | `/admin/leen-huur/{loan}/edit` | A V* B AT IS AD | admin only |
| 74 | GET|HEAD | `/admin/leen-huur/{loan}/overeenkomst` | A V* B AT IS AD | admin only |
| 75 | GET|HEAD | `/admin/leen-huur/{loan}/photo/{photo}` | A V* B AT IS AD | admin only |
| 76 | DELETE | `/admin/leen-huur/{loan}/photo/{photo}` | A V* B AT IS AD | admin only |
| 77 | POST | `/admin/leen-huur/{loan}/retour` | A V* B AT IS AD | admin only |
| 78 | GET|HEAD | `/admin/lidmaatschap` | A V* B AT IS AD | admin only |
| 79 | GET|HEAD | `/admin/lidmaatschap/data` | A V* B AT IS AD | admin only |
| 80 | GET|HEAD | `/admin/lidmaatschap/prijs` | A V* B AT IS AD | admin only |
| 81 | POST | `/admin/lidmaatschap/prijs` | A V* B AT IS AD | admin only |
| 82 | GET|HEAD | `/admin/lidmaatschap/{lidmaatschap}` | A V* B AT IS AD | admin only |
| 83 | DELETE | `/admin/lidmaatschap/{lidmaatschap}` | A V* B AT IS AD | admin only |
| 84 | GET|HEAD | `/admin/lidmaatschap/{lidmaatschap}/factuur` | A V* B AT IS AD | admin only |
| 85 | GET|HEAD | `/admin/mailinglijst` | A V* B AT IS AD | admin only |
| 86 | POST | `/admin/mailinglijst` | A V* B AT IS AD | admin only |
| 87 | GET|HEAD | `/admin/mailinglijst/verzenden` | A V* B AT IS AD | admin only |
| 88 | POST | `/admin/mailinglijst/verzenden` | A V* B AT IS AD | admin only |
| 89 | DELETE | `/admin/mailinglijst/{id}` | A V* B AT IS AD | admin only |
| 90 | DELETE | `/admin/mass-emails/{id}` | A V* B AT IS AD | admin only |
| 91 | GET|HEAD | `/admin/monteur` | A V* B AT IS AD | admin only |
| 92 | GET|HEAD | `/admin/monteur/data` | A V* B AT IS AD | admin only |
| 93 | GET|HEAD | `/admin/monteur/tarieven` | A V* B AT IS AD | admin only |
| 94 | POST | `/admin/monteur/tarieven` | A V* B AT IS AD | admin only |
| 95 | GET|HEAD | `/admin/monteur/{monteur}` | A V* B AT IS AD | admin only |
| 96 | DELETE | `/admin/monteur/{monteur}` | A V* B AT IS AD | admin only |
| 97 | GET|HEAD | `/admin/monteur/{monteur}/factuur` | A V* B AT IS AD | admin only |
| 98 | GET|HEAD | `/admin/notificaties` | A V* B AT IS AD | admin only |
| 99 | POST | `/admin/notificaties/test` | A V* B AT IS AD | admin only |
| 100 | POST | `/admin/notificaties/tokens` | A V* B AT IS AD | admin only |
| 101 | DELETE | `/admin/notificaties/tokens/{fcmToken}` | A V* B AT IS AD | admin only |
| 102 | GET|HEAD | `/admin/orders` | A V* B AT IS AD | admin only |
| 103 | GET|HEAD | `/admin/orders/data` | A V* B AT IS AD | admin only |
| 104 | GET|HEAD | `/admin/orders/new-count` | A V* B AT IS AD | admin only |
| 105 | GET|HEAD | `/admin/orders/{order}` | A V* B AT IS AD | admin only |
| 106 | DELETE | `/admin/orders/{order}` | A V* B AT IS AD | admin only |
| 107 | GET|HEAD | `/admin/orders/{order}/invoice` | A V* B AT IS AD | admin only |
| 108 | POST | `/admin/orders/{order}/status` | A V* B AT IS AD | admin only |
| 109 | GET|HEAD | `/admin/purchase-sales` | A V* B AT IS AD | admin only |
| 110 | POST | `/admin/purchase-sales` | A V* B AT IS AD | admin only |
| 111 | GET|HEAD | `/admin/purchase-sales/create` | A V* B AT IS AD | admin only |
| 112 | PUT | `/admin/purchase-sales/{id}` | A V* B AT IS AD | admin only |
| 113 | DELETE | `/admin/purchase-sales/{id}` | A V* B AT IS AD | admin only |
| 114 | GET|HEAD | `/admin/purchase-sales/{id}/edit` | A V* B AT IS AD | admin only |
| 115 | GET|HEAD | `/admin/rekenmachine` | A V* B AT IS AD | admin only |
| 116 | GET|HEAD | `/admin/reparatie-aanmeldingen` | A V* B AT IS AD | admin only |
| 117 | GET|HEAD | `/admin/reparatie-aanmeldingen/data` | A V* B AT IS AD | admin only |
| 118 | GET|HEAD | `/admin/reparatie-aanmeldingen/new-count` | A V* B AT IS AD | admin only |
| 119 | GET|HEAD | `/admin/reparatie-aanmeldingen/{repairSubmission}` | A V* B AT IS AD | admin only |
| 120 | DELETE | `/admin/reparatie-aanmeldingen/{repairSubmission}` | A V* B AT IS AD | admin only |
| 121 | GET|HEAD | `/admin/reparatie-aanmeldingen/{repairSubmission}/photo/{file}` | A V* B AT IS AD | admin only |
| 122 | POST | `/admin/reparatie-aanmeldingen/{repairSubmission}/status` | A V* B AT IS AD | admin only |
| 123 | GET|HEAD | `/admin/shipping` | A V* B AT IS AD | admin only |
| 124 | POST | `/admin/shipping` | A V* B AT IS AD | admin only |
| 125 | GET|HEAD | `/admin/shipping/data` | A V* B AT IS AD | admin only |
| 126 | PUT | `/admin/shipping/{shipping}` | A V* B AT IS AD | admin only |
| 127 | DELETE | `/admin/shipping/{shipping}` | A V* B AT IS AD | admin only |
| 128 | POST | `/admin/shipping/{shipping}/toggle` | A V* B AT IS AD | admin only |
| 129 | GET|HEAD | `/admin/users` | A V* B AT IS AD | admin only |
| 130 | POST | `/admin/users` | A V* B AT IS AD | admin only |
| 131 | GET|HEAD | `/admin/users/data` | A V* B AT IS AD | admin only |
| 132 | GET|HEAD | `/admin/users/{klant}` | A V* B AT IS AD | admin only |
| 133 | PUT | `/admin/users/{klant}` | A V* B AT IS AD | admin only |
| 134 | DELETE | `/admin/users/{klant}` | A V* B AT IS AD | admin only |
| 135 | POST | `/admin/users/{klant}/role` | A V* B AT IS AD | admin only |
| 136 | POST | `/admin/users/{klant}/toggle-block` | A V* B AT IS AD | admin only |
| 137 | GET|HEAD | `/admin/webshop/bestanden` | A V* B AT IS AD | admin only |
| 138 | POST | `/admin/webshop/bestanden/chunk` | A V* B AT IS AD | admin only |
| 139 | POST | `/admin/webshop/bestanden/complete` | A V* B AT IS AD | admin only |
| 140 | GET|HEAD | `/admin/webshop/bestanden/data` | A V* B AT IS AD | admin only |
| 141 | DELETE | `/admin/webshop/bestanden/{file}` | A V* B AT IS AD | admin only |
| 142 | GET|HEAD | `/admin/webshop/categories` | A V* B AT IS AD | admin only |
| 143 | POST | `/admin/webshop/categories` | A V* B AT IS AD | admin only |
| 144 | GET|HEAD | `/admin/webshop/categories/data` | A V* B AT IS AD | admin only |
| 145 | GET|HEAD | `/admin/webshop/categories/{category}` | A V* B AT IS AD | admin only |
| 146 | PUT | `/admin/webshop/categories/{category}` | A V* B AT IS AD | admin only |
| 147 | DELETE | `/admin/webshop/categories/{category}` | A V* B AT IS AD | admin only |
| 148 | POST | `/admin/webshop/categories/{category}/toggle` | A V* B AT IS AD | admin only |
| 149 | GET|HEAD | `/admin/webshop/coupons` | A V* B AT IS AD | admin only |
| 150 | POST | `/admin/webshop/coupons` | A V* B AT IS AD | admin only |
| 151 | GET|HEAD | `/admin/webshop/coupons/data` | A V* B AT IS AD | admin only |
| 152 | GET|HEAD | `/admin/webshop/coupons/{coupon}` | A V* B AT IS AD | admin only |
| 153 | PUT | `/admin/webshop/coupons/{coupon}` | A V* B AT IS AD | admin only |
| 154 | DELETE | `/admin/webshop/coupons/{coupon}` | A V* B AT IS AD | admin only |
| 155 | POST | `/admin/webshop/coupons/{coupon}/toggle` | A V* B AT IS AD | admin only |
| 156 | GET|HEAD | `/admin/webshop/license-codes` | A V* B AT IS AD | admin only |
| 157 | POST | `/admin/webshop/license-codes` | A V* B AT IS AD | admin only |
| 158 | GET|HEAD | `/admin/webshop/license-codes/data` | A V* B AT IS AD | admin only |
| 159 | DELETE | `/admin/webshop/license-codes/{licenseCode}` | A V* B AT IS AD | admin only |
| 160 | GET|HEAD | `/admin/webshop/products` | A V* B AT IS AD | admin only |
| 161 | POST | `/admin/webshop/products` | A V* B AT IS AD | admin only |
| 162 | GET|HEAD | `/admin/webshop/products/create` | A V* B AT IS AD | admin only |
| 163 | GET|HEAD | `/admin/webshop/products/data` | A V* B AT IS AD | admin only |
| 164 | POST | `/admin/webshop/products/generate-description` | A V* B AT IS AD | admin only |
| 165 | GET|HEAD | `/admin/webshop/products/{product}` | A V* B AT IS AD | admin only |
| 166 | PUT | `/admin/webshop/products/{product}` | A V* B AT IS AD | admin only |
| 167 | DELETE | `/admin/webshop/products/{product}` | A V* B AT IS AD | admin only |
| 168 | GET|HEAD | `/admin/webshop/products/{product}/edit` | A V* B AT IS AD | admin only |
| 169 | POST | `/admin/webshop/products/{product}/toggle` | A V* B AT IS AD | admin only |
| 170 | POST | `/admin/webshop/products/{product}/toggle-featured` | A V* B AT IS AD | admin only |
| 171 | GET|HEAD | `/admin/webshop/reviews/data` | A V* B AT IS AD | admin only |
| 172 | GET|HEAD | `/admin/webshop/reviews/product/{product}` | A V* B AT IS AD | admin only |
| 173 | DELETE | `/admin/webshop/reviews/{review}` | A V* B AT IS AD | admin only |
| 174 | POST | `/admin/webshop/reviews/{review}/approve` | A V* B AT IS AD | admin only |
| 175 | POST | `/admin/webshop/reviews/{review}/reject` | A V* B AT IS AD | admin only |
| 176 | GET|HEAD | `/afspraak` | — | PUBLIC |
| 177 | POST | `/afspraak/submit` | T5,1 | PUBLIC |
| 178 | POST | `/ai-chat/close` | T10,1 | PUBLIC |
| 179 | POST | `/ai-chat/handover` | T10,1 | PUBLIC |
| 180 | GET|HEAD | `/ai-chat/history` | T30,1 | PUBLIC |
| 181 | GET|HEAD | `/ai-chat/messages` | T60,1 | PUBLIC |
| 182 | POST | `/ai-chat/offline` | T5,1 | PUBLIC |
| 183 | GET|HEAD | `/ai-chat/photo/{message}` | T60,1 | PUBLIC |
| 184 | POST | `/ai-chat/rate` | T10,1 | PUBLIC |
| 185 | POST | `/ai-chat/send` | T30,1 | PUBLIC |
| 186 | POST | `/ai-chat/start` | T10,1 | PUBLIC |
| 187 | GET|HEAD | `/ai-chat/status` | — | PUBLIC |
| 188 | GET|HEAD | `/betaal/login` | — | PUBLIC |
| 189 | POST | `/betaal/login` | T10,1 | PUBLIC |
| 190 | GET|HEAD | `/cart` | — | PUBLIC |
| 191 | DELETE | `/cart` | T10,1 | PUBLIC |
| 192 | GET|HEAD | `/cart/count` | — | PUBLIC |
| 193 | POST | `/cart/coupon` | T20,1 | PUBLIC |
| 194 | DELETE | `/cart/coupon` | T20,1 | PUBLIC |
| 195 | POST | `/cart/items` | T30,1 | PUBLIC |
| 196 | PATCH | `/cart/items/{item}` | T30,1 | PUBLIC |
| 197 | DELETE | `/cart/items/{item}` | T30,1 | PUBLIC |
| 198 | GET|HEAD | `/checkout` | — | PUBLIC |
| 199 | POST | `/checkout` | T10,1 | PUBLIC |
| 200 | POST | `/checkout/totals` | T30,1 | PUBLIC |
| 201 | GET|HEAD | `/confirm-password` | A | any logged-in user |
| 202 | POST | `/confirm-password` | A | any logged-in user |
| 203 | GET|HEAD | `/contact` | — | PUBLIC |
| 204 | POST | `/contact/submit` | T5,1 | PUBLIC |
| 205 | GET|HEAD | `/diensten/{slug}` | — | PUBLIC |
| 206 | GET|HEAD | `/download/bestand/{file}` | T120,1 | PUBLIC |
| 207 | POST | `/email/verification-notification` | A T6,1 | any logged-in user |
| 208 | GET|HEAD | `/firebase-messaging-sw.js` | — | PUBLIC |
| 209 | GET|HEAD | `/forgot-password` | G | guests only |
| 210 | POST | `/forgot-password` | G | guests only |
| 211 | GET|HEAD | `/lid-worden` | — | PUBLIC |
| 212 | POST | `/lid-worden` | T5,1 | PUBLIC |
| 213 | GET|HEAD | `/lid-worden/failed/{lidmaatschap}` | — | PUBLIC |
| 214 | GET|HEAD | `/lid-worden/return/{lidmaatschap}` | — | PUBLIC |
| 215 | GET|HEAD | `/lid-worden/success/{lidmaatschap}` | — | PUBLIC |
| 216 | POST | `/lid-worden/webhook` | — | PUBLIC |
| 217 | GET|HEAD | `/login` | G | guests only |
| 218 | POST | `/login` | G | guests only |
| 219 | POST | `/logout` | A | any logged-in user |
| 220 | GET|HEAD | `/mijn-bestellingen` | A | any logged-in user |
| 221 | GET|HEAD | `/mijn-bestellingen/{orderNumber}` | A | any logged-in user |
| 222 | GET|HEAD | `/mijn-bestellingen/{orderNumber}/factuur` | A | any logged-in user |
| 223 | GET|HEAD | `/over-ons` | — | PUBLIC |
| 224 | PUT | `/password` | A | any logged-in user |
| 225 | GET|HEAD | `/payment/failed` | — | PUBLIC |
| 226 | GET|HEAD | `/payment/return/{order}` | — | PUBLIC |
| 227 | GET|HEAD | `/payment/success` | — | PUBLIC |
| 228 | POST | `/payment/webhook` | — | PUBLIC |
| 229 | GET|HEAD | `/privacy` | — | PUBLIC |
| 230 | GET|HEAD | `/profile` | A | any logged-in user |
| 231 | PATCH | `/profile` | A | any logged-in user |
| 232 | DELETE | `/profile` | A | any logged-in user |
| 233 | GET|HEAD | `/register` | — | PUBLIC |
| 234 | POST | `/register` | — | PUBLIC |
| 235 | GET|HEAD | `/reparatie-aanmelden` | — | PUBLIC |
| 236 | POST | `/reparatie/submit` | T5,1 | PUBLIC |
| 237 | POST | `/reset-password` | G | guests only |
| 238 | GET|HEAD | `/reset-password/{token}` | G | guests only |
| 239 | GET|HEAD | `/sitemap.xml` | — | PUBLIC |
| 240 | GET|HEAD | `/storage/{path}` | — | PUBLIC (signature-gated, see note) |
| 241 | PUT | `/storage/{path}` | — | PUBLIC (signature-gated, see note) |
| 242 | GET|HEAD | `/stream/video/{file}` | — | PUBLIC |
| 243 | GET|HEAD | `/tarieven` | — | PUBLIC |
| 244 | POST | `/technician/check-coupon` | T30,1 | PUBLIC (now: auth + operator role) |
| 245 | GET|HEAD | `/technician/failed/{form}` | — | PUBLIC |
| 246 | POST | `/technician/payment/submit` | T10,1 | PUBLIC (now: auth + operator role) |
| 247 | GET|HEAD | `/technician/payment/{klantnummer}` | — | PUBLIC (now: auth + operator role) |
| 248 | POST | `/technician/quote` | T30,1 | PUBLIC (now: auth + operator role) |
| 249 | GET|HEAD | `/technician/return/{form}` | — | PUBLIC |
| 250 | GET|HEAD | `/technician/success/{form}` | — | PUBLIC (payer-session/operator gated) |
| 251 | POST | `/technician/webhook` | — | PUBLIC |
| 252 | GET|HEAD | `/track` | — | PUBLIC |
| 253 | POST | `/track` | — | PUBLIC (now: throttle:10,1) |
| 254 | GET|HEAD | `/up` | — | PUBLIC |
| 255 | GET|HEAD | `/verify-email` | A | any logged-in user |
| 256 | GET|HEAD | `/verify-email/{id}/{hash}` | A S T6,1 | any logged-in user |
| 257 | GET|HEAD | `/voorwaarden` | — | PUBLIC |
| 258 | GET|HEAD | `/webshop/{categorySlug}/{productSlug}` | — | PUBLIC |
| 259 | POST | `/webshop/{categorySlug}/{productSlug}/reviews` | T5,1 | PUBLIC |
| 260 | GET|HEAD | `/webshop/{slug}` | — | PUBLIC |
| 261 | GET|HEAD | `/wishlist` | A | any logged-in user |
| 262 | POST | `/wishlist/toggle` | A T30,1 | any logged-in user |
| 263 | DELETE | `/wishlist/{favorite}` | A T30,1 | any logged-in user |
| 264 | GET|HEAD | `/zoeken` | — | PUBLIC |

Note: the real route list has 264 routes. Rows 244/246/247/248, 250, 253
and the auth POST throttles (rows 202/210/234/237) changed after the snapshot — see the deltas listed
above the table.
