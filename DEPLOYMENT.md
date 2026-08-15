# Deployment — Hostinger auto-deploy from GitHub

Pushing to `main` is enough. The server pulls and deploys automatically via
a cron job, no webhook or panel needed.

## Architecture

```
you:  git push origin main
         │
GitHub: main (public repo)
         │  (git fetch + reset --hard, every 5 min via server cron)
         ▼
Hostinger: /home/u439113944/domains/slimmepc.kulshy.online/public_html  (git checkout)
         │  scripts/deploy.sh (only runs deploy steps when code changed)
         ▼
         composer install --no-dev
         php artisan migrate --force
         php artisan optimize:clear
```

The deploy is triggered by a server-side `crontab` line. There is no GitHub
webhook, so deploys land within the cron interval (5 minutes).

## Server facts

| Item      | Value |
|-----------|-------|
| SSH host  | `82.25.102.153` |
| SSH port  | `65002` |
| SSH user  | `u439113944` |
| SSH alias | `ssh slimmepc` (defined in `~/.ssh/config`) |
| App dir   | `/home/u439113944/domains/slimmepc.kulshy.online/public_html` |
| Branch    | `main` |
| Remote    | `origin` → `https://github.com/yisefmahdi/slimmepc.git` |
| Deploy log| `storage/logs/deploy.log` inside the app dir |
| Cron      | `*/5 * * * * /bin/bash /home/u439113944/domains/slimmepc.kulshy.online/public_html/scripts/deploy.sh >/dev/null 2>&1` |

The SSH **password** is stored only in the local, gitignored file
`deploy-credentials.local.md`. It is deliberately NOT committed: the GitHub
repo is public, so committing it would leak it.

## Deploying

### Automatic
```sh
git push origin main
# within ~5 minutes the server is live; verify:
ssh slimmepc "tail -n 20 /home/u439113944/domains/slimmepc.kulshy.online/public_html/storage/logs/deploy.log"
```

### Manual (immediate)
```sh
git push origin main
ssh slimmepc "cd /home/u439113944/domains/slimmepc.kulshy.online/public_html && bash scripts/deploy.sh"
```

## `scripts/deploy.sh`

- `git fetch origin main` + `git reset --hard origin/main` → the server is a
  clean mirror of GitHub. Do not hand-edit files in the checkout; they are
  wiped on the next deploy.
- Exits early when nothing changed.
- Runs deploy steps only when the code changed.
- `.env`, `storage/` (runtime files, uploads) are gitignored — never touched
  by the reset.
- Logs to `storage/logs/deploy.log`, auto-truncated at 5 MB.

## Server one-time setup (done 2026-08-15)

1. SSH key `~/.ssh/slimmepc_deploy` (public key in `slimmepc_deploy.pub`)
   added to `~/.ssh/authorized_keys` on the server so deploys never need a
   password.
2. `~/.ssh/config` on the dev machine defines the `slimmepc` alias.
3. Cron installed on the server (see table above).
4. First run of `scripts/deploy.sh` verified against the log.

## Troubleshooting

| Symptom | Cause / fix |
|---------|-------------|
| Log: `git fetch failed` | Git/SSH state on the server; run git commands manually. |
| Log: `migrate FAILED` | Schema error; fix locally, push, redeploy. |
| Site old but log says "already up to date" | Commit not pushed, or on wrong branch. |
| Manual server edits disappear | Expected — reset mirrors GitHub. |
| Need immediate deploy | Manual flow above. |

## Related

- opencode skill: `.opencode/skills/deploy/SKILL.md`
- opencode command: `/deploy` (`.opencode/command/deploy.md`)
- Local credentials (never committed): `deploy-credentials.local.md`