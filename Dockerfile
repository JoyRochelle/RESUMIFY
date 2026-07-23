FROM node:20-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
COPY public ./public
RUN npm run build

FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        chromium fonts-liberation libzip-dev libpng-dev libonig-dev libcurl4-openssl-dev unzip git \
    && docker-php-ext-install pdo_mysql mbstring zip gd bcmath curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

ENV TEMPLATE_THUMBNAIL_BROWSER=/usr/bin/chromium

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader --no-progress

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize \
    && chmod +x docker/entrypoint.sh \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080
CMD ["docker/entrypoint.sh"]
