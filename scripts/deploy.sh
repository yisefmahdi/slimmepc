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
set -u

APP_DIR="/domains/slimmepc.kulshy.online/public_html"
BRANCH="main"
REMOTE="origin"
LOG_FILE="$APP_DIR/storage/logs/deploy.log"
MAX_LOG_BYTES=5242880

log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >> "$LOG_FILE"; }

mkdir -p "$(dirname "$LOG_FILE")"

# Truncate the log once it grows past MAX_LOG_BYTES
if [ -f "$LOG_FILE" ] && [ "$(stat -c%s "$LOG_FILE" 2>/dev/null || echo 0)" -gt "$MAX_LOG_BYTES" ]; then
  : > "$LOG_FILE"
fi

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

if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction >> "$LOG_FILE" 2>&1 \
    && log "composer install ok" || log "composer install FAILED"
else
  log "composer not found, skipping"
fi

php artisan migrate --force >> "$LOG_FILE" 2>&1 \
  && log "migrate ok" || log "migrate FAILED"

php artisan optimize:clear >> "$LOG_FILE" 2>&1 \
  && log "cache clear ok" || log "cache clear FAILED"

log "deploy check done"