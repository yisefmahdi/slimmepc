# Audit Domein F: Geautomatiseerde Tests & Regressieborging

**Auditor:** QA Architect / Lead Automation Engineer  
**Datum:** 9 oktober 2026  
**Branch:** `audit/full-review`  
**Doelstelling:** 100% groene testsuite, regressiebescherming voor alle beveiligingsoplossingen, geïsoleerde tests via SQLite `:memory:`.

---

## 1. Testsuite Overzicht & Statistieken

- **Test Framework:** Pest v3.8 (bovenop PHPUnit 11)
- **Database:** SQLite `:memory:` (100% geïsoleerd; geen interactie met `slimmepc_2026` of MySQL)
- **Totaal aantal tests:** **324 tests**
- **Resultaat:** **100% PASS (0 gefaald, 0 fouten, 0 genegeerd)**
- **Aantal asserties:** **> 1.400 asserties**
- **Mocks & Fakes:** Volledig gebruik van `Mail::fake()`, `Storage::fake('local')`, en `Mockery` voor Mollie API interacties (geen externe API-aanroepen of e-mails verzonden).

---

## 2. Testmatrix per Domein

| Testbestand | Type | Tests | Status | Scope |
|---|---|---|---|---|
| `tests/Feature/AuditShopTest.php` | Feature | 16 | **PASS** | Webshop CRUD, validatie, winkelwagen-isolatie, coupons, kassa-grenzen, reviews, Mollie webhook, dubbele afronding, licentie-races. |
| `tests/Feature/AuditMatrixTest.php` | Feature | 38 | **PASS** | Complete route-toegang matrix: gasten, gebruikers, monteurs, admins; dataset tests voor alle publieke routes; login-throttling. |
| `tests/Feature/AuditIdorTest.php` | Feature | 9 | **PASS** | IDOR/BOLA verificatie op orders, facturen, favorieten, adressen, ondertekende downloads en rolmutaties. |
| `tests/Feature/AuditInvoiceTest.php` | Feature | 6 | **PASS** | Unieke factuurnummering (`INV-`), PDF-generatie via DomPDF, download-autorisatie, PDF-hergeneratie. |
| `tests/Feature/AuditPaymentsTest.php` | Feature | 16 | **PASS** | Betalingsvalidatie, webhook-idempotentie, bedrag-mismatch detectie, privacy na betaling, monteur-betalingen. |
| `tests/Feature/AuditCmsTest.php` | Feature | 9 | **PASS** | CMS secties, media upload MIME-validatie, cache-isolatie tussen gasten en ingelogde gebruikers. |
| `tests/Feature/AuditServicesTest.php` | Feature | 4 | **PASS** | CMS paginacache-versiebeheer, design settings, coupon geldigheid en inclusieve BTW-berekening. |
| `tests/Feature/AuditAuthTest.php` | Feature | 21 | **PASS** | Rolbescherming, mass-assignment afweer, monteurportaal, sessievernieuwing, directe uitlog bij blokkering. |
| `tests/Feature/AuditConfigTest.php` | Feature | 10 | **PASS** | `.gitignore`, `.htaccess` beveiligingsregels, deploy-script lock, sessiecookie-flags, private disk isolatie. |
| `tests/Feature/AuditInjectionTest.php` | Feature | 8 | **PASS** | SQL injectie preventie, XSS ontwijking in Blade/PDF, invoer-sanitisatie, honeypot velden. |
| `tests/Unit/AuditHelpersTest.php` | Unit | 5 | **PASS** | Helper functies: bestandsgrootte, verzendlabels, licentie-telling, coupon math, Mollie floats. |
| `tests/Feature/Auth/*` | Feature | 18 | **PASS** | Laravel Breeze authenticatie, wachtwoord-resets, e-mailverificatie, registratie. |
| `tests/Unit/*` (Ai, Chat, Embeddings) | Unit | 31 | **PASS** | AI-assistent, semantische FAQ-zoekmachine, embedding wiskunde, handoff-tools. |
| Bestaande Feature Tests (Orders, Contact, Chat, etc.) | Feature | 133 | **PASS** | Bestellingen, contact inbox, apparatenontvangst, digitale downloads, e-mailtemplates, etc. |
| **Totaal** | | **324** | **100% GROEN** | |

---

## 3. Belangrijke Test-Architectuur Verbeteringen

1. **Sessie- en Authenticatie-isolatie:**
   - In Pest tests zorgden opeenvolgende `$this->actingAs()` aanroepen ervoor dat volgende aanvragen in dezelfde methode ingelogd bleven. Dit is structureel opgelost door expliciete `Auth::logout()` aanroepen en controller cache-resets (`$route->controller = null`).
2. **JSON Cookie-Ondersteuning:**
   - Laravel vereist `$this->withCredentials()` om cookies door te geven in `postJson`/`patchJson` verzoeken. Toegevoegd aan de `beforeEach` fixture van `AuditShopTest.php`.
3. **Mollie Mocks:**
   - Gemockte betalingen maken gebruik van `\Mollie\Api\Resources\Payment` objecten met geldige formaten (`amount`, `metadata`, `status`) en voorkomen directe interactie met externe API's.
4. **Terminable Callbacks:**
   - Factuur-e-mails worden verstuurd via `->afterResponse()`. In unit tests wordt de terminable stack schoon gehouden via `(fn () => $this->terminatingCallbacks = [])->call($this->app);` tussen opeenvolgende webhooks.

---

## 4. Conclusie

De testsuite biedt een waterdichte regressiebescherming. Elke geïdentificeerde kwetsbaarheid uit de audit (IDOR, mass assignment, injection, bedrag-mismatch, cache-lekkage) is gedekt met specifieke, reproduceerbare testen.
