---
name: deploy
description: Use when deploying or updating the slimmepc Laravel site on the Hostinger server, when a push to GitHub must reach the live site, when checking that the production server is in sync with GitHub, or when troubleshooting the auto-deploy cron, git pull, composer, migrations, or cache on the server. Covers the automatic cron deploy, manual SSH deploys, and deploy troubleshooting.
---

# Deploy (Hostinger auto-deploy)

## How a deploy happens

Pushing to `main` on GitHub is enough. The server auto-deploys within ~5
minutes via a cron job that runs `scripts/deploy.sh` from the git checkout.

## Server facts

- Host: `82.25.102.153`, port `65002`
- SSH user: `u439113944`
- App dir (git checkout): `/domains/slimmepc.kulshy.online/public_html`
- Branch: `main`
- SSH alias (configured in `~/.ssh/config`): `ssh slimmepc`
- Log: `storage/logs/deploy.log` inside the app dir
- The SSH **password** is NOT in the repo. Read the local, gitignored file
  `deploy-credentials.local.md` only when a password is actually required.
  NEVER write the password into the repository or into this skill.

## Deploy flow

### Automatic (default)
1. Commit and push: `git push origin main`.
2. Wait up to 5 minutes for the server cron.
3. Verify: `ssh slimmepc "tail -n 20 /domains/slimmepc.kulshy.online/public_html/storage/logs/deploy.log"`

### Manual (immediate)
1. `git push origin main`.
2. Run the deploy script on the server:
   `ssh slimmepc "cd /domains/slimmepc.kulshy.online/public_html && bash scripts/deploy.sh"`
3. Confirm the same tail command as above.

## What `scripts/deploy.sh` does

- `git fetch origin main` then `git reset --hard origin/main` — the server is
  a clean mirror of GitHub. Do NOT hand-edit files inside the checkout; they
  are wiped on the next deploy.
- Skips all deploy steps when the code did not change ("already up to date").
- Runs, only when code changed: `composer install --no-dev --optimize-autoloader
  --prefer-dist`, `php artisan migrate --force`, `php artisan optimize:clear`.
- `.env`, `storage/` (incl. runtime files and uploads) are gitignored and are
  NOT touched by the reset.
- Logs every run to `storage/logs/deploy.log` (auto-truncated at 5 MB).

## Cron (server side)

Installed with `crontab -e` on the server. Inspect with `ssh slimmepc "crontab -l"`.
Line format (5-minute interval):
`*/5 * * * * /bin/bash /domains/slimmepc.kulshy.online/public_html/scripts/deploy.sh >/dev/null 2>&1`

## Troubleshooting

- Log says `git fetch failed` / `git reset --hard ... failed` → SSH or git
  state problem on the server; connect and run the git commands by hand.
- Log says `migrate FAILED` → schema problem; read the log tail for the SQL
  error, fix locally, push, redeploy.
- Site shows old code but log says "already up to date" → local commit was
  never pushed, or you are on a branch other than `main`.
- Manual edits on the server disappear → expected; the reset mirrors GitHub.
- Want a manual deploy without waiting for cron → use the Manual flow above.

## One-time server setup (already done unless the server was recreated)

1. Authorize the SSH key (`~/.ssh/slimmepc_deploy.pub`) in
   `~/.ssh/authorized_keys` on the server.
2. Make sure `public_html` is a git checkout of this repo with remote `origin`
   pointing at GitHub `main`.
3. Install the cron line above.
4. Run `bash scripts/deploy.sh` once and confirm the log.