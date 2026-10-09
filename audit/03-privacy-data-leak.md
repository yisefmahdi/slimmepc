# Audit Domein C: Datalekken & Privacy (Data Leakage & Exfiltration)

**Auditor:** Application Security Auditor / Privacy Specialist  
**Datum:** 9 oktober 2026  
**Branch:** `audit/full-review`  
**Prioriteit:** HOOGSTE PRIORITEIT (Zero Data Exfiltration)

---

## 1. Samenvatting

| Inspectiepunt | Beoordeling | Beschermingsmaatregel |
|---|---|---|
| **Klant PII & Wachtwoord-hashes** | **VEILIG** | `$hidden = ['password', 'remember_token']` op `User`; API resource endpoints selecteren expliciete velden. |
| **Digitale Productbestanden** | **VEILIG** | Opgeslagen op niet-publieke `local` schijf (`storage/app/private/files`). Nooit gesymlinked naar `public/storage`. Toegang strikt via HMAC-ondertekende of eigenaar-gevalideerde route (`DownloadController`). |
| **Factuur PDF's (Order & Handmatig)** | **VEILIG** | Opgeslagen in `storage/app/invoices/orders` en `invoices/manual`. Toegang vereist authenticatie als order-eigenaar of beheerder. Geen directe URL serving. |
| **Volledige Pagina Cache (HTML)** | **VEILIG** | Alleen actief voor niet-ingelogde gasten (`!Auth::check()`). Geen PII of gepersonaliseerde winkelwagendata gecached. Dynamische CSRF-token verversing gewaarborgd. |
| **Database Dumps & Legacy Scripts** | **VEILIG** | `.gitignore` en `.htaccess` blokkeren alle `*.sql`, `scripts/backups/`, `.env*` en logbestanden. Geen credentials in git tracking. |
| **Mailinglijst Exfiltratie** | **VEILIG** | Strikt beveiligd achter `admin` middleware; exporteerbare tabellen alleen voor admins; uitschrijf-tokens cryptografisch gehasht. |
| **Beveiligingsheaders & Cookies** | **VEILIG** | Cookies geflagged met `HttpOnly` en `SameSite=Lax`. `.htaccess` dwingt X-Content-Type-Options, X-Frame-Options en Referrer-Policy af. |

---

## 2. Gedetailleerde Bevindingen & Remediatie

### [PRIV-01] [Kritiek - Opgelost] Isolatie van Beschermde Digitale Downloads
- **Risico:** Exfiltratie van betaalde software/installatiebestanden en licenties via directe HTTP toegang of onbeveiligde URL's.
- **Inspectie:**
  - `FilesController` slaat uploads op via chunks in `storage/app/private/files/`.
  - Er bestaat **geen** publieke symlink naar `storage/app/private`.
  - `DownloadController` valideert:
    1. Is de gebruiker `admin`? -> Toegestaan.
    2. Is de gebruiker eigenaar van een betaalde order (`payment_status === 'paid'`) waarin dit bestand zit? -> Toegestaan.
    3. Beschikt de aanvraag over een geldige, niet-verlopen HMAC URL-handtekening gekoppeld aan het specifieke order-ID? -> Toegestaan.
    4. In alle andere gevallen: `abort(403)`.
- **Testdekking:** `tests/Feature/DigitalFilesTest.php` en `tests/Feature/AuditIdorTest.php`.
- **Status:** PASS (Volledig beschermd).

---

### [PRIV-02] [Hoog - Opgelost] Toegangsbeveiliging op Factuur-PDF's
- **Risico:** Onbevoegd inzien van klantadressen, telefoonnummers, gekochte producten en factuurbedragen via enumeratie van factuurnummers.
- **Inspectie:**
  - Facturen (`OrderInvoice`) gebruiken willekeurige alfanumerieke identifiers (`INV-2026-XXXXXX`) in plaats van sequentiële getallen.
  - Endpoint `GET /account/bestellingen/{order}/factuur`:
    - Als gast: redirect naar `/login`.
    - Als ingelogde gebruiker A bij order van B: `403 Forbidden` (`$order->user_id !== Auth::id()`).
    - Admin endpoint `/admin/orders/{order}/invoice` vereist `admin` rol middleware.
- **Testdekking:** `tests/Feature/AccountOrdersTest.php`, `tests/Feature/AuditInvoiceTest.php`.
- **Status:** PASS (Geen data exfiltratie mogelijk).

---

### [PRIV-03] [Hoog - Opgelost] Gast Full-Page Cache Lekpreventie
- **Risico:** Per ongeluk serveren van gepersonaliseerde pagina's (sessies, gebruikersnamen, winkelwageninhoud) van bezoeker A aan bezoeker B via de 1-maands CMS paginacache.
- **Inspectie (`PageController.php` & `CmsCacheService`):**
  - Caching vindt uitsluitend plaats indien `!$request->user()` én er geen sessie-authenticatie aanwezig is.
  - Pagina's met formulieren of dynamische status (`afspraak`, `contact`, `cart`, `checkout`, `account/*`) hebben de cache expliciet omzeild met `no-store` headers.
  - In `AuditCmsTest.php` is geverifieerd dat een ingelogde sessie altijd de dynamische rendering triggert en nooit een gedeelde gastcache ophaalt of overschrijft.
- **Testdekking:** `tests/Feature/AuditCmsTest.php`.
- **Status:** PASS.

---

### [PRIV-04] [Medium - Opgelost] Minimalisatie van PII in Admin Datatabellen
- **Risico:** JSON endpoints die gevoelige hash-waarden of interne velden retourneren naar admin frontend tabellen.
- **Inspectie:**
  - `User` model verbergt standaard `password` en `remember_token`.
  - Admin `KlantController` selecteert expliciete kolommen: `id`, `name`, `email`, `role`, `is_blocked`, `klantnummer`, `created_at`.
  - `LicenseCodeController` toont codes alleen in gemaskeerde of beheerder-geautoriseerde context; verkochte codes kunnen niet worden overschreven of verwijderd.
- **Status:** PASS.

---

### [PRIV-05] [Medium - Opgelost] Webserver (.htaccess) en Git Immuniteit voor Gevoelige Bestanden
- **Risico:** Direct downloaden van `.env`, `.git`, SQLite databases, migratiedumps (`*.sql`) of logbestanden via webbrowsers.
- **Inspectie:**
  - `public/.htaccess` bevat strikte RewriteRules die toegang blokkeren tot verborgen bestanden, configuraties en dumps:
    ```apache
    RewriteRule ^(\.env|\.git|composer\.|storage/logs|.*\.sql|.*\.log) - [F,L,NC]
    ```
  - `.gitignore` dekt alle `*.sql`, `scripts/backups/`, `storage/logs/*.log`, en database configuratiebestanden.
- **Testdekking:** `tests/Feature/AuditConfigTest.php`.
- **Status:** PASS.

---

## 3. Conclusie Privacy & Datalekken

Er zijn geen actieve datalekken, PII exfiltratieroutes of onbeveiligde publieke schijflocaties aangetroffen. Alle gevoelige klantinformatie, digitale leveringen en administratieve exports zijn strikt afgeschermd achter multi-factor ownership checks en rol-gebaseerde middleware.
