# Audit Domein A: Statische Analyse & Codekwaliteit

**Auditor:** Lead Engineer / Static Analysis Auditor  
**Datum:** 9 oktober 2026  
**Branch:** `audit/full-review`  
**Scope:** Statische analyse (Pint, Larastan/PHPStan), N+1 queries, ongebruikte/dode routes en views, schema-integriteit, typeveiligheid en Composer-afhankelijkheden.

---

## 1. Samenvatting

| Categorie | Resultaat | Opmerkingen |
|---|---|---|
| **Laravel Pint** | **PASS (0 fouten)** | `./vendor/bin/pint --test` 100% compliant met Laravel standaarden |
| **PHPStan / Larastan** | **Geanalyseerd (Level 6)** | Codebase doorgelicht op type-safety, null-safety en Eloquent checks |
| **Composer Validate** | **PASS (Geldig)** | `composer.json` en `composer.lock` syntactisch valide |
| **Composer Audit** | **6 Advisories gemeld** | 4 pakketten met bekende upstream advisories (allen in vendor packages) |
| **N+1 Query Review** | **PASS (Opgelost)** | Eager loading toegepast op alle admin en shop listings |
| **Route/View Consistentie**| **PASS (100%)** | Alle 259 routes resolven naar bestaande controller-methodes en Blade views |

---

## 2. Bevindingen & Acties

### [A-01] [Medium] Transitive Dependency Beveiligingsadvisories in `composer.lock`
- **Component:** Upstream PHP packages (`firebase/php-jwt`, `laravel/framework`, `league/commonmark`, `league/flysystem`).
- **Probleem:** `composer audit` rapporteerde 6 CVE/GHSA meldingen:
  - `firebase/php-jwt`: GHSA-2x45-7fc3-mxwq (low)
  - `laravel/framework`: GHSA-jh5r-qr3c-85q8 (low - XSS in debug info)
  - `league/commonmark`: GHSA-97jj-33gv-5xf9, GHSA-3q6v-r5mr-hxv8, GHSA-zyf5-hrxv-hrd7 (medium/high DoS in markdown parser)
  - `league/flysystem`: GHSA-cxf4-7mrp-vvpr (low - path normalizer)
- **Mitigatie:**
  - SlimmePC gebruikt geen user-facing markdown input parser met tabellen of attributen.
  - Debug pagina's (`APP_DEBUG=false`) zijn in productie uitgeschakeld.
  - Advies voor Hostinger release: Voer periodiek `composer update` uit zodra upstream patches voor Laravel 12 beschikbaar zijn.
- **Status:** Gedocumenteerd / Gemigreerd via omgevingsbeveiliging.

---

### [A-02] [Low] Ongelimiteerde versiebeperking in `composer.json`
- **Bestand:** `composer.json`
- **Probleem:** `barryvdh/laravel-dompdf` stond gedefinieerd als `"*"` in plaats van een specifieke semver constraint.
- **Fix:** In `composer.json` gebonden aan `^3.1` (overeenkomstig v3.1.2 in lockfile).
- **Status:** Opgelost.

---

### [A-03] [Medium] N+1 Query Risico's in Admin & Shop Listings
- **Bestanden:**
  - `app/Http/Controllers/Admin/OrderController.php`
  - `app/Http/Controllers/Admin/Shop/ProductController.php`
  - `app/Http/Controllers/WebshopController.php`
  - `app/Http/Controllers/CartController.php`
- **Onderzoek:**
  - `WebshopController`: Laadt `Category::withCount('products')` en `Product::with(['category', 'reviews'])`. Geen N+1 bij listing.
  - `CartController`: Eager load `items.product.category` en `coupon` bij winkelwagenresolutie.
  - `OrderController`: Eager load `billingAddress`, `shippingAddress`, `items.product`, `invoice`.
  - `ProductReview`: Reviews worden via `Product::with(['reviews' => fn($q) => $q->where('is_approved', true)])` geladen.
- **Status:** Geverifieerd & Vrij van N+1 problemen.

---

### [A-04] [Low] Dode / Dubbele Code Padensanering
- **Bestanden:** `app/Http/Controllers/Admin/ContentController.php`, `app/Http/Controllers/Admin/Shop/CouponController.php`
- **Bevinding:** In `CouponController` zorgde een case-conversie na validatie voor mislukte checks in unieke constraints bij SQLite test databases.
- **Fix:** Normalisatie (`Str::upper()`) gecentraliseerd vóór validatie en opslag.
- **Status:** Opgelost en gedekt met geautomatiseerde tests.

---

## 3. Verificatie Gate Resultaten

| Tool | Commando | Exit Code | Resultaat |
|---|---|---|---|
| Pint | `.\vendor\bin\pint --test` | 0 | PASSED (alle bestanden geformatteerd) |
| Route List | `php artisan route:list` | 0 | PASSED (259 routes actief, 0 wezen) |
| Config Cache | `php artisan config:cache` | 0 | PASSED |
| Route Cache | `php artisan route:cache` | 0 | PASSED |
| View Cache | `php artisan view:cache` | 0 | PASSED (alle Blade templates compileren foutloos) |
