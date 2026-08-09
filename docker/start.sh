#!/bin/sh
set -eu

KEY_MATERIAL="${APP_KEY:-nexarch-local-development-key}"
case "$KEY_MATERIAL" in
  base64:*) ;;
  *) APP_KEY="base64:$(printf '%s' "$KEY_MATERIAL" | openssl dgst -sha256 -binary | openssl base64 -A)"; export APP_KEY ;;
esac

php artisan config:cache
exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
