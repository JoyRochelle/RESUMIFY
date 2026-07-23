#!/bin/sh
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link || true

if [ "${TEMPLATE_THUMBNAILS_FORCE:-false}" = "true" ]; then
    php artisan templates:thumbnails --force || echo "Template thumbnail generation failed; continuing with iframe fallback."
else
    php artisan templates:thumbnails || echo "Template thumbnail generation failed; continuing with iframe fallback."
fi

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"
