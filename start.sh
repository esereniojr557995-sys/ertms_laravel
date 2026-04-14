#!/bin/bash
set -e

echo "==> Creating storage symlink..."
php artisan storage:link || true

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding database..."
php artisan db:seed --force

echo "==> Starting Apache..."
exec apache2-foreground