#!/bin/sh
set -e

if [ "${DB_AUTO_MIGRATE:-false}" = "true" ] || [ "${DB_AUTO_MIGRATE:-0}" = "1" ]; then
  php /var/www/html/scripts/init-database.php
fi

exec "$@"
