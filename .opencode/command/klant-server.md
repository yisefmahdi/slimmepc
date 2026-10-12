---
description: Work on the CLIENT production server slimme-pc.nl (mijndomein/Plesk) — inspect, upload, verify, or troubleshoot. For the dev/staging server use the deploy command instead.
agent: build
---

Work on the klant-server (mijndomein production, slimme-pc.nl). Follow the `klant-server` skill for server facts, quoting rules, and safeguards.

1. Inspect first (read-only): `ssh mijndomein ...`, page checks via local `curl.exe`, log tails.
2. Backup before any write (DB via HTTP-importer pattern, files already covered by `backup/` + `backup-old-live-*/` — never touch those).
3. Execute the change (scp upload / HTTP script with random token).
4. Verify (pages 200/301, counts, error logs clean).
5. Clean up temp files, tokens, dumps and PII everywhere (server + local temp).
6. Document in `project-structure.md` when the change is structural.

SSH alias `mijndomein` (= `f_00094667@server061.mijndomeinhosting.nl`, key `~/.ssh/mijndomein_migration`). No php/mysql/composer/git on that server — DB work only via tokenized HTTP scripts. Quoting: single-quotes only inside `ssh '...'` (double quotes are stripped in transit).
