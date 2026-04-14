#!/bin/bash
set -e

echo "==> Creating storage symlink..."
php artisan storage:link || true

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Starting Apache..."
exec apache2-foreground