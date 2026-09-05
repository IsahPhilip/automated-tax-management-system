#!/usr/bin/env bash
set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET_DIR="${DEPLOY_TARGET:-/var/www/automated-tax-management-system}"
REMOTE_HOST="${DEPLOY_HOST:-}"
REMOTE_USER="${DEPLOY_USER:-root}"

if [[ -z "$REMOTE_HOST" ]]; then
  echo "DEPLOY_HOST is not set. Set it before running deployment."
  exit 1
fi

cd "$APP_ROOT"
composer install --no-interaction --prefer-dist --no-progress --no-scripts --no-dev
php -d memory_limit=-1 vendor/bin/phpunit --configuration phpunit.xml --colors=never

rsync -az --delete \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='.env' \
  --exclude='.env.example' \
  --exclude='vendor/' \
  --exclude='storage/logs/' \
  --exclude='storage/uploads/' \
  "$APP_ROOT/" "$REMOTE_USER@$REMOTE_HOST:$TARGET_DIR/"

echo "Deployment completed to $REMOTE_USER@$REMOTE_HOST:$TARGET_DIR"
