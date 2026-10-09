#!/usr/bin/env bash
#
# slimmepc — Hostinger auto-deploy
#
# Pulls the latest from GitHub and runs the Laravel deploy steps.
# Idempotent: exits early when the server is already up to date.
# Installed as a cron job on the server (see DEPLOYMENT.md).
#
# Only safe on a server that mirrors GitHub exactly. Any local edits
# inside the git checkout are overwritten by the hard reset.
#
# Rollback: the previous + new commit hashes are logged on every run.
#   git reset --hard <previous-hash> && php artisan migrate --force \
#     && php artisan config:cache && php artisan route:cache && php artisan view:cache
#
set -u

APP_DIR="/home/u439113944/domains/slimmepc.kulshy.online/public_html"
BRANCH="main"
REMOTE="origin"
LOG_FILE="$APP_DIR/storage/logs/deploy.log"
MAX_LOG_BYTES=5242880
LOCK_FILE="$APP_DIR/storage/deploy.lock"

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >> "$LOG_FILE"; }

mkdir -p "$(dirname "$LOG_FILE")"

# Truncate the log once it grows past MAX_LOG_BYTES
if [ -f "$LOG_FILE" ] && [ "$(stat -c%s "$LOG_FILE" 2>/dev/null || echo 0)" -gt "$MAX_LOG_BYTES" ]; then
  : > "$LOG_FILE"
fi

# Cron-overlap lock: skip this run when a previous deploy is still active.
exec 9>"$LOCK_FILE" || { log "ERROR: cannot open lock $LOCK_FILE"; exit 1; }
if ! flock -n 9; then
  log "deploy already running, skipping"
  exit 0
fi

bring_up() {
  php artisan up >> "$LOG_FILE" 2>&1 || true
}
fail() {
  log "ERROR: $*"
  bring_up
  log "deploy FAILED (rolled maintenance mode back up)"
  exit 1
}

cd "$APP_DIR" || { log "ERROR: cannot cd $APP_DIR"; exit 1; }

log "deploy check start"

OLD_HEAD=$(git rev-parse HEAD 2>/dev/null || true)

if ! git fetch "$REMOTE" "$BRANCH" >> "$LOG_FILE" 2>&1; then
  log "ERROR: git fetch failed"
  exit 1
fi

if ! git reset --hard "$REMOTE/$BRANCH" >> "$LOG_FILE" 2>&1; then
  log "ERROR: git reset --hard $REMOTE/$BRANCH failed"
  exit 1
fi

NEW_HEAD=$(git rev-parse HEAD 2>/dev/null || true)

if [ -n "$OLD_HEAD" ] && [ "$OLD_HEAD" = "$NEW_HEAD" ]; then
  log "already up to date ($NEW_HEAD)"
  exit 0
fi

log "updated: ${OLD_HEAD:-none} -> $NEW_HEAD"

php artisan down --render="errors.503" --retry=60 >> "$LOG_FILE" 2>&1 \
  || log "WARNING: maintenance mode down failed, continuing"

if command -v composer >/dev/null 2>&1; then
  if ! composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction >> "$LOG_FILE" 2>&1; then
    fail "composer install FAILED (vendor left untouched by follow-up steps)"
  fi
  log "composer install ok"
else
  fail "composer not found"
fi

if ! php artisan migrate --force >> "$LOG_FILE" 2>&1; then
  fail "migrate FAILED"
fi
log "migrate ok"

# Sitemap verversen met de APP_URL van de server (canonieke absolute URL's voor Google).
php artisan sitemap:generate >> "$LOG_FILE" 2>&1 \
  && log "sitemap ok" || log "sitemap FAILED (non-fatal)"

php artisan config:cache >> "$LOG_FILE" 2>&1 \
  && log "config cache ok" || log "config cache FAILED (non-fatal)"
php artisan route:cache >> "$LOG_FILE" 2>&1 \
  && log "route cache ok" || log "route cache FAILED (non-fatal)"
php artisan view:cache >> "$LOG_FILE" 2>&1 \
  && log "view cache ok" || log "view cache FAILED (non-fatal)"

bring_up

log "deploy check done ($NEW_HEAD)"
