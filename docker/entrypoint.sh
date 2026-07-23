#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link || true
php artisan templates:thumbnails || echo "Template thumbnail generation failed; continuing with iframe fallback."

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
