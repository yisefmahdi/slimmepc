# SlimmePC Audit — 05 Payments, Orders, Downloads & Business-Logic Abuse (Sub-agent E)

Branch: `audit/full-review`. Scope: Mollie flow (webshop + lidmaatschap + technician),
cart/checkout pricing, coupons, digital delivery (licences + protected files), invoice
numbering/PDF access (SLM-/INV-/SLP-/LID-/ORD-/AF-), afspraak/contact abuse, AI endpoint.
All verification with mocked Mollie (fake `MolliePaymentService` bound in the container,
real `Mollie\Api\Resources\Payment` value objects — never the live API), SQLite `:memory:`,
`Mail::fake()` / `Storage::fake()`. No secrets printed, committed or mailed.

## Verified OK (no issue, no change needed)

- **Webhook authenticity**: all three webhooks re-fetch the payment from Mollie by the posted
  `id` (`PaymentController@webhook`, `LidmaatschapController@webhook`, `TechnicianController@webhook`);
  the posted ID alone never marks anything paid. Order/form lookup uses server-set Mollie metadata.
- **Server-side pricing**: checkout totals, shipping, membership price (`MembershipSetting::price()`),
  technician quote (`pricingFor` + `calculate`) all compute server-side; digital carts force `digital`
  shipping even when the request tampers `shipping_method`.
- **Licence assignment** is transactional with `lockForUpdate` + per-item idempotency; codes are only
  rendered to the paid-order owner (account page, gated by `payment_status === 'paid'`) and in the
  invoice e-mail to the buyer's own address.
- **Download HMAC**: Laravel signed routes (APP_KEY HMAC, `hash_equals`), order+file binding,
  live `payment_status === 'paid'` re-check, `throttle:120,1`, `no-store` + `noindex` headers.
- **Invoice PDF access**: account invoice is strictly user-scoped; all admin invoice
  downloads/previews sit behind `admin` middleware. SLM- numbering has unique loop + unique index.
- **AI key handling**: `OPENAI_API_KEY` stays server-side (`OpenAiClient` → `Http`), never sent to the client.

---

### [PAY-01] Mollie webhooks/returns finalized without verifying the paid amount
- Severity: High
- Category: Payment
- Location: `app/Http/Controllers/PaymentController.php:43,69`, `app/Http/Controllers/LidmaatschapController.php:180,206`, `app/Http/Controllers/TechnicianController.php:451,475` (routes `/payment/webhook`, `/lid-worden/webhook`, `/technician/webhook` + all three return routes)
- Description / Proof: `isPaid($payment)` was the only gate before `finalizeOrder`/`finalizeMembership`/`finalizeForm`. A payment whose collected amount differed from the order total (total edited after payment creation, test-mode games, partial capture) would still fulfil the order, assign licences and send the invoice. Regression test posts a webhook with a €1.00 fake payment for a €121.00 order and asserts `amount-mismatch` + no invoice.
- Impact: fulfilment of underpaid orders (goods, licences, invoices, member benefits).
- Fix applied: new `MolliePaymentService::amountMatches()` (`app/Services/Payments/MolliePaymentService.php:70`, exact-cent string compare, tolerant of Mollie Money object/array shapes); all three webhooks + all three return paths now require it, else mark failed/cancelled and report. 0bc565f
- Regression test: `tests/Feature/AuditPaymentsTest.php` — "refuses to finalize when the Mollie amount mismatches", "consumes the coupon on technician finalize and enforces the amount" (underpay half).
- Status: Fixed

### [PAY-02] Public return/success pages exposed order PII via enumerable IDs
- Severity: High
- Category: AuthZ
- Location: `app/Http/Controllers/PaymentController.php:61,99,119,135` (routes `GET /payment/return/{order}`, `GET /payment/success?order=`, `GET /payment/failed?order=`), `resources/views/landing/payment-success.blade.php`
- Description / Proof: `return()`/`success()`/`failed()` loaded any order by sequential id with no ownership check; the success view printed items, totals **and the buyer's full e-mail address**, and `success()` even "celebrated" unpaid orders. `GET /payment/return/123` as a stranger showed another customer's email + basket.
- Impact: customer PII leak (email, basket, totals) by trivial id enumeration; misleading success state.
- Fix applied: `mayViewOrder()` (owner via `user_id`, guest via `owned_orders` session binding set at `CheckoutController@store`, admins always); strangers get the generic success page; `success()` only shows paid + viewable orders; buyer e-mail removed from the blade. 0bc565f
- Regression test: "hides order details on the return page from strangers", "shows order details … to the session-bound guest buyer", "never celebrates an unpaid order".
- Status: Fixed

### [PAY-03] Technician payment flow reachable without login; client PII by klantnummer
- Severity: High
- Category: AuthZ
- Location: `routes/web.php:102-105` (`/technician/payment/{klantnummer}`, `/technician/payment/submit`, `/technician/quote`, `/technician/check-coupon`), `app/Http/Controllers/TechnicianController.php:101-116`
- Description / Proof: `paymentPage()` showed client name/address/email/phone for any `SLP-######` (1M space, enumerable) with zero auth, and `storePaymentForm()` let anyone create payable technician invoices. `GET /technician/payment/SLP-777001` as guest returned 200 with client PII.
- Impact: mass client-PII harvesting; fraudulent payable forms; coupon burn (see PAY-05).
- Fix applied: `auth` middleware on the four operator routes + `requireTechnician()` role gate (technician/admin, blocked-aware; JSON → 403, web → technician login). Note: the `requireTechnician()` gate itself was written by the auth sub-agent ([AUTH-02]); this audit added the route middleware, the finding/tests. 0bc565f
- Regression test: "blocks guests and customers from the technician payment page".
- Status: Fixed

### [PAY-04] Membership/technician success pages exposed PII via enumerable IDs
- Severity: High
- Category: AuthZ
- Location: `app/Http/Controllers/LidmaatschapController.php:230,301` (`/lid-worden/success/{id}`), `app/Http/Controllers/TechnicianController.php:497,589` (`/technician/success/{form}`)
- Description / Proof: both pages only required `payment_status === 'paid'`; sequential ids meant any stranger could read member/form details (name, email on the lidmaatschap page, amount on the technician page).
- Impact: member PII leak by enumeration.
- Fix applied: session binding set in `mollieReturn()` + `mayViewMembership()`/`mayViewForm()` (owner/admin/session, else 404); buyer e-mail line removed from `lidmaatschap-success.blade.php`. 0bc565f
- Regression test: "hides membership success pages from strangers".
- Status: Fixed

### [PAY-05] Technician coupon burned before payment + min_amount bypass
- Severity: High
- Category: Payment
- Location: `app/Http/Controllers/TechnicianController.php:232` (`checkCouponModel`), `:537-551` (`finalizeForm`)
- Description / Proof: `storePaymentForm()` created `CouponUsage` + incremented `used_count` for **unpaid** forms (abandoned forms permanently burned single-use codes and inflated global counts — verified pre-fix), and `checkCouponModel()` ignored `Coupon::min_amount` (a min-€500 code applied to a €15 job; webshop path enforces it). Test asserts zero usage / zero increment after an unpaid submit.
- Impact: destroyed single-use coupons, wrong global usage counts, unjustified discounts.
- Fix applied: consumption moved to `finalizeForm()` (post-payment, re-validated, `firstOrCreate` + increment-only-on-create); `min_amount` enforced in `checkCouponModel()`. 0bc565f
- Regression test: "does not burn a coupon until the technician payment succeeds", "rejects technician coupons below their minimum amount", "consumes the coupon on technician finalize…".
- Status: Fixed

### [PAY-06] Double finalize race (webhook + return) could duplicate invoices/licences/mails
- Severity: Medium
- Category: Payment
- Location: `app/Services/OrderPaymentService.php:34`, `app/Http/Controllers/LidmaatschapController.php:248`, `app/Http/Controllers/TechnicianController.php:532`
- Description / Proof: the paid-check then update was non-atomic, so a webhook and a return arriving together could both pass the check; the second `OrderInvoice::create` would hit the unique index (500 → Mollie retries) and mails/licence work could double-run.
- Impact: 500s on Mollie callbacks, duplicate e-mails, retry storms; worst case (without the unique index) duplicate invoices.
- Fix applied: `DB::transaction()` + `lockForUpdate()` on the order/membership/form row in all three finalize paths; losers become clean no-ops. 0bc565f
- Regression test: "treats a repeated webhook as a no-op (single invoice)".
- Status: Fixed

### [PAY-07] Per-order signed download links never expired
- Severity: Medium
- Category: AuthZ
- Location: `app/Models/DigitalFile.php:52` (`signedUrlForOrder`, used by `DigitalDelivery` + invoice e-mail)
- Description / Proof: `URL::signedRoute()` has no `expires` — a leaked/forwarded guest link worked forever (only the paid-check bounded it). Test asserts the generated URL now carries `expires=` + `signature=`.
- Impact: indefinite bearer capability on paid digital goods.
- Fix applied: `URL::temporarySignedRoute(..., now()->addDays(30), ...)`; `hasValidSignature()` enforces expiry (behaviour change flagged: links in old e-mails keep working since they were permanent; new links last 30 days). 0bc565f
- Regression test: "issues expiring signed download links".
- Status: Fixed

### [PAY-08] LID- invoice numbers had no uniqueness check
- Severity: Medium
- Category: Bug
- Location: `app/Http/Controllers/LidmaatschapController.php:107,289`
- Description / Proof: `'LID-'.random_int(100000, 999999)` (900k space) with no exists-loop; a collision would 500 on the unique index mid-checkout. Test creates two memberships and asserts distinct `LID-` numbers.
- Impact: sporadic 500s / duplicate-key errors on signup spikes.
- Fix applied: `makeInvoiceNumber()` with exists-loop (unique DB index remains the final guard). 0bc565f
- Regression test: "creates unique membership invoice numbers…".
- Status: Fixed

### [PAY-09] SLP- technician invoice numbers from predictable `uniqid()`
- Severity: Medium
- Category: Bug
- Location: `app/Models/TechnicianInvoice.php:30-41`
- Description / Proof: `'SLP-'.strtoupper(uniqid())` is time-based (predictable invoice ids) and can collide under concurrency (unique index → 500 in `storePaymentForm`).
- Impact: guessable invoice numbers; rare duplicate-key 500s.
- Fix applied: `Str::random(8)` + exists retry loop. 0bc565f
- Regression test: covered indirectly by "consumes the coupon on technician finalize…" (invoice auto-number created twice without collision).
- Status: Fixed

### [PAY-10] Admin could delete already-sold licence codes
- Severity: Medium
- Category: AuthZ
- Location: `app/Http/Controllers/Admin/Shop/LicenseCodeController.php:91`
- Description / Proof: `destroy()` deleted unconditionally — removing a `sold` code silently robbed a paying customer of their licence (still referenced from the paid order). Test asserts 422 + row preserved for sold, 200 for available.
- Impact: destruction of proof-of-purchase / customer licences.
- Fix applied: 422 unless `status === 'available'` and `order_id === null`. 0bc565f
- Regression test: "refuses to delete a sold license code".
- Status: Fixed

### [PAY-11] Contact form sent e-mail synchronously (mail-flood / DoS amplifier)
- Severity: Medium
- Category: Config
- Location: `app/Http/Controllers/ContactController.php:38-51` (`POST /contact/submit`)
- Description / Proof: two SMTP sends ran inside the request on a throttled public endpoint — a slow mail server held connections open per spam submit (the sibling afspraak/order flows already defer via `afterResponse()`).
- Impact: connection exhaustion, mail-queue flooding via the public form.
- Fix applied: sends moved into `dispatch(...)->afterResponse()` (existing `ContactSubmitTest` still passes: 19/19). 0bc565f
- Regression test: existing `tests/Feature/ContactSubmitTest.php` ("stores a contact submission and sends the confirmation e-mail").
- Status: Fixed

### [PAY-12] Afspraak sequential numbers raced under concurrency
- Severity: Low
- Category: Bug
- Location: `app/Http/Controllers/AfspraakController.php:18-70`
- Description / Proof: read-max-then-insert with no guard; two concurrent submits computed the same `AF-YYYY-NNNNN` and one 500'd on the unique index.
- Impact: sporadic 500s on the public appointment form.
- Fix applied: `nextNumber()` + retry loop on SQLSTATE 23000 (up to 5 attempts). 0bc565f
- Regression test: logic covered by existing afspraak flow; race window is timing-dependent (not deterministically testable on SQLite).
- Status: Fixed

### [PAY-13] Afspraak form had no honeypot (contact form does)
- Severity: Low
- Category: Config
- Location: `app/Http/Requests/StoreAfspraakSubmissionRequest.php:37-38`, `app/Http/Controllers/AfspraakController.php:21`
- Description / Proof: only `throttle:5,1` stood between bots and inbox/mail/push fan-out. Test posts with `website` filled → 422.
- Impact: spam appointments → admin e-mail/push noise.
- Fix applied: `website => prohibited` honeypot rule (added by fellow agent; this audit added the controller `unset()` + test). (commit hash — joint change)
- Regression test: "rejects afspraak honeypot fills".
- Status: Fixed

### [PAY-14] Download filename reached Content-Disposition unsanitized
- Severity: Low
- Category: Injection
- Location: `app/Http/Controllers/DownloadController.php:65`
- Description / Proof: `$file->name` comes from an admin upload (`basename()` only) and was passed straight to `response()->download()` — embedded CRLF/quotes could inject response headers.
- Impact: header injection on the download response (admin-triggered).
- Fix applied: strip `\r \n "` + `basename()`, fallback name. 0bc565f
- Regression test: none deterministic (header-level); covered by code review.
- Status: Fixed

### [PAY-15] AI product endpoint: unthrottled, unbounded prompt, raw provider errors
- Severity: Medium
- Category: Config
- Location: `routes/admin.php:312`, `app/Http/Controllers/Admin/Shop/AiProductController.php:17-46`
- Description / Proof: no throttle on a paid-per-token call; `features` array had no item cap (each entry grows the prompt); catch-block returned `$e->getMessage()` (provider internals: key names, quota/billing state) to the browser. Admin-only bounds it to session theft / rogue admin, but cost amplification is real.
- Impact: API-cost abuse; internal error disclosure.
- Fix applied: `throttle:10,1`; `features => max:20`; generic user message + `report($e)`. 0bc565f
- Regression test: "keeps the AI endpoint admin-only and caps feature input".
- Status: Fixed

### [PAY-16] AI-generated HTML is rendered unescaped; instructions are raw prompt input
- Severity: Low
- Category: Injection
- Location: `resources/views/landing/product-details.blade.php:393` (`{!! $product->description !!}`), `app/Services/Ai/Prompts/ProductPromptBuilder.php:88-90`
- Description / Proof: `additional_instructions` is concatenated verbatim into the LLM prompt and the model output is echoed unescaped on the product page. Trigger requires an admin session (self-harm / stolen-session only), and admins can already author HTML via TinyMCE, so exploitability is marginal — but a prompt-injected `<script>`/`<form>` in AI output would persist as stored XSS for all visitors.
- Impact: stored XSS via AI output (admin-triggered).
- Fix applied: Not fixed — reason: output-sanitization policy (allowlist HTML purifier on save vs render) is a product decision affecting the whole CMS, owned by the XSS agent (Sub-agent D). Input caps from PAY-15 reduce prompt size only.
- Regression test: none (policy pending).
- Status: Needs Human Decision

### [PAY-17] Physical stock is never decremented — overselling possible
- Severity: Medium
- Category: Bug
- Location: `app/Http/Controllers/CheckoutController.php:161-169`, `app/Services/OrderPaymentService.php` (no stock mutation anywhere; `products` has only a `stock_status` flag, no quantity column)
- Description / Proof: checkout validates `stock_status === 'in_stock'` but nothing ever flips or counts it down; two buyers can purchase the last physical item concurrently with no race protection at all.
- Impact: overselling physical goods.
- Fix applied: Not fixed — reason: needs a schema decision (quantity column + decrement-on-finalize with locks, or explicit manual-stock workflow). Flagged for product owner.
- Regression test: none.
- Status: Needs Human Decision

### [PAY-18] Single-use coupon reuse across concurrent pending checkouts
- Severity: Low
- Category: Payment
- Location: `app/Services/CartService.php:239-250`, `app/Services/OrderPaymentService.php:42-52`, `coupon_usages` migration (no unique `coupon_id × user_id`)
- Description / Proof: single-use is checked against `coupon_usages`, which is only written at finalize — two pending carts can both hold the same single-use coupon; both finalizes `firstOrCreate` (one wins the row) but both orders keep the discount and `used_count` increments twice (non-atomic `increment` outside the order lock… now inside the finalize transaction, but the cross-order race remains).
- Impact: double discount on a single-use code (narrow race window, requires concurrent checkouts).
- Fix applied: Partially fixed — finalize path hardened (transactional, increment-only-on-create in technician flow); full fix needs a unique constraint + reservation model. Left open to avoid a migration other agents may also touch.
- Regression test: none deterministic (race); single-use happy path covered by existing coupon tests.
- Status: Partially fixed

### [PAY-19] Generous download throttle, no per-order download cap
- Severity: Low
- Category: Config
- Location: `routes/web.php:126-128` (`throttle:120,1` on `/download/bestand/{file}`)
- Description / Proof: 120 hits/min/IP with unlimited downloads per paid order; link-sharing abuse is bounded only by the (now expiring, PAY-07) signature and the paid-check. Tightening risks breaking legit multi-file customers.
- Impact: link-sharing / bandwidth abuse at the margins.
- Fix applied: Not fixed — reason: threshold is a business decision (flagged).
- Regression test: none.
- Status: Needs Human Decision

### [PAY-20] Duplicate active memberships via double submit race
- Severity: Low
- Category: Bug
- Location: `app/Http/Controllers/LidmaatschapController.php:53-65`
- Description / Proof: the active-membership check is a non-atomic read before create; two rapid submits for the same e-mail can both pass and create two paid memberships. `throttle:5,1` + distinct e-mail requirement make it narrow.
- Impact: double billing for one member (refund workflow).
- Fix applied: Not fixed — reason: proper fix is a partial unique index / state machine, touching membership schema owned elsewhere; risk/benefit favours flagging.
- Regression test: none.
- Status: Open
