---
name: klant-server
description: Use when working on the CLIENT production server (slimme-pc.nl on mijndomein/Plesk) — inspecting files or logs, uploading code or assets, importing the database, verifying pages/redirects/counts, setting cron tasks, or troubleshooting anything on slimme-pc.nl. This is the LIVE client site; the dev/staging server is covered by the `deploy` skill (Hostinger). NEVER touch backup/ or backup-old-live-*/ and always clean up temp files + tokens afterwards.
---

# Klant-server (mijndomein/Plesk productieserver — slimme-pc.nl)

> **WAARSCHUWING — secrets in dit bestand.** Deze SKILL bevat het live
> database-wachtwoord omdat de eigenaar dat expliciet eiste (sneller werken).
> Dit bestand staat in de repo en gaat mee met elke `git push` — iedereen met
> repo-toegang kan het lezen, en het blijft voor altijd in de git-geschiedenis.
> **Advies: roteer het DB-wachtwoord via Plesk zodra deze skill niet meer
> dagelijks nodig is, en verplaats secrets dan naar een gitignored
> `mijndomein-credentials.local.md` (zelfde patroon als `deploy`).**

## Server facts

- Host: `server061.mijndomeinhosting.nl` (ook: `194.213.127.21`, `slimme-pc.nl` wijst hierheen)
- SSH user: `f_00094667` (NIET de FTP-user `f_0009edb7334` — die heeft geen SSH)
- SSH key (lokaal): `~/.ssh/mijndomein_migration` (alleen public key staat op de server)
- SSH alias (lokaal, `~/.ssh/config`): `ssh mijndomein`
- Docroot: `~/httpdocs` = `/var/www/vhosts/slimme-pc.nl/httpdocs` (Plesk-docroot = project-ROOT, géén `public/` subdir!)
- Database: `h_00094667_slimmepc`, user `h_00094667_h_00094667_user`, password `^SqM5Smmkl7w2r!z` (zie waarschuwing boven)
- Werkmap (buiten docroot!): `~/migrate/` (= `/var/www/vhosts/slimme-pc.nl/migrate/`)
- SQLite/mysql-CLI: **afwezig**. PHP-CLI: **afwezig**. Wel: web-PHP **8.4** via HTTPS.
- Logs: `~/logs/error_log` (Apache/PHP) + `~/httpdocs/storage/logs/laravel.log` (app)
- PII-regel: dumps, `.env`-kopieën en scripts met wachtwoorden/tokens **altijd** na gebruik verwijderen — lokaal én op de server.

## Chroot-beperkingen (Plesk; alleen deze tools bestaan)

Aanwezig: `bash cat chmod cp curl du grep groups gunzip gzip head id less ln ls mkdir more mv pwd rm rmdir scp sh tail tar touch unzip vi wget zip` (+ `mysql`/`mysqldump` **NIET**, `php`/`composer`/`git`/`sed`/`awk`/`cut`/`wc`/`find`/`xargs`/`printf` **NIET**).
Gevolg: DB-werk altijd via **token-beveiligde HTTP-scripts** (web-PHP), nooit via CLI. `exec()` werkt in web-PHP; `passthru/system/shell_exec/popen` zijn disabled.

## Quoting-regels (verplicht — anders breken commando's stilzwijgend)

Windows-OpenSSH stript **dubbele quotes** tijdens transport naar de server:
- Gebruik in `ssh '...'` **uitsluitend single-quotes**; PowerShell-escaping = verdubbelen (`''`).
- Nooit `"` in remote-commando's (inclusief `grep "x"`, `echo "..."`, JSON met `"`).
- `|` en haakjes alleen binnen single-quoted patterns.
- Wachtwoorden nooit als CLI-argument (`--pass=` breekt bij speciale tekens) — altijd via **`--config=db.json`** (PHP→PHP via bestand) of KEY=value-env-bestanden.
- PowerShell heeft geen `grep/cut/wc/Join-String` (v5.1): gebruik `Select-String`, vermijd `Join-String` (wel `-join`).

## Omgeving-gotchas (echt tegengekomen)

- `open_basedir` (web-PHP) = `/var/www/vhosts/slimme-pc.nl/:/tmp/` — werkbestanden MOETEN onder de vhost-dir (`~/migrate/`), nergens anders.
- Web-PHP ziet een **andere `/tmp`** (PrivateTmp) dan SSH — deel nooit bestanden via `/tmp` tussen SSH en web.
- PDO `host=localhost` (socket) faalt → altijd **`127.0.0.1`** (TCP) gebruiken.
- `.ssh/authorized_keys` eindigde zonder newline — sleutel plakte vast (ongeldig). Eerst lege `echo` voor newline, dan pas appenden; rechten `~/.ssh`=700, `authorized_keys`=600.
- `bootstrap/cache/` + `storage/framework/{cache,sessions,views}` nooit meekopiëren (paden van andere server); dirs leeg opnieuw aanmaken + `chmod 775 storage bootstrap/cache`.
- Route/view-cache kan niet via artisan gewist worden (geen CLI) — views hercompileren vanzelf (mtime), routes worden nooit gecachet (nooit `route:cache` draaien zonder CLI-toegang!).
- `public/` bestaat niet als docroot-subdir: root-`index.php` (paden `__DIR__.'/vendor...'`, géén `/../`), root-`.htaccess` (= public-variant + storage-rewrite), `assets` is symlink `assets → public/assets`, `public/`-map hersteld voor `filemtime(public_path())`.
- Storage-rewrite mét loop-guard (zonder guard: AH00124 na 10 internal redirects → alle `/storage/*` 500):
  `RewriteCond %{REQUEST_URI} !^/storage/app/public/` + `RewriteRule ^storage/(.*)$ storage/app/public/$1 [L]`
- Deze root-`.htaccess` is een **server-specifieke kopie** (staat NIET zo in de repo) — bij repo-updates nooit blind overschrijven.
- `.env` staat in temp/ (nooit in repo) en wordt per `scp` geüpload; `APP_DEBUG=false`, `APP_URL=https://slimme-pc.nl`, `FIREBASE_CREDENTIALS=/var/www/vhosts/slimme-pc.nl/private/firebase-service-account.json` ( géén `/private/...` — open_basedir!).

## Standaardflows

### Inspecteren (alleen lezen)
- `ssh mijndomein 'ls ~/httpdocs/'`, `tail -n 30 ~/logs/error_log`, `tail -c 3000 ~/httpdocs/storage/logs/laravel.log`
- Pagina-check: `curl.exe -s -o NUL -w "%{http_code} %{redirect_url}" "https://slimme-pc.nl/<pad>"` (lokaal; `grep` bestaat niet in PS 5.1 → `Select-String`)

### Bestanden uploaden
- `scp <lokaal> mijndomein:httpdocs/<relatief-pad>` (overschrijft direct — geen git/deploy hier!)
- Na PHP-wijzigingen: geen cache-clear nodig; bij twijfel `curl` de pagina + check `error_log`.

### Database importeren (geen mysql-CLI!)
Alleen via tijdelijk HTTP-script met random token: auto-backup oude DB → `backup-old-db.sql`, DROP ALL, chunked steps à ~300 statements met resume-state, counts-check, daarna script + state + env + dump **direct verwijderen**. Nooit dump.sql in docroot laten staan.

### Tellingen verifiëren
`scripts/count-tables.php` (CLI op dev/Hostinger) + token-variant via HTTPS op klant-server; snapshots bewaren als `~/migrate/row-counts-*.json`. Alleen `cache/sessions/carts/fcm_tokens` mogen verschillen (verkeer); de rest moet exact gelijk zijn.

## Nooit doen

- `backup/` of `backup-old-live-*/` aanraken (lezen om te verifiëren mag).
- Wachtwoorden, dumps of `.env`-kopieën achterlaten op de server of in de repo.
- Temp HTTP-scripts (import/tellers/debug) laten staan na gebruik.
- `TRUNCATE`/DROP op live zonder backup én zonder expliciete gebruikersbevestiging.
- `migratie-sleutel.txt`-achtige bestanden in docroot laten staan.
- Eigen SSH-key (`mijndomein_migration`) verwijderen zonder overleg (nodig voor vervolgwerk).

## Rest-acties klant (Plesk-panel — kunnen niet via SSH)

PHP-versie domein (8.2+), SSL-certificaat, Scheduled Tasks (`schedule:run` + `queue:work` — vereist voor mails/inbound), Search Console-sitemap, admin-login-test, Mollie-testbetaling.
