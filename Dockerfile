# ---------- 1) Composer deps ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --no-interaction
COPY . .
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-progress --no-interaction

# ---------- 2) Frontend build ----------
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- 3) PHP runtime (FPM) ----------
FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www/html

# System libs & PHP extensions
RUN apk add --no-cache bash libjpeg-turbo-dev libpng-dev libzip-dev oniguruma-dev icu-dev \
 && docker-php-ext-configure gd \
 && docker-php-ext-install pdo_mysql gd opcache intl zip

# Copy app code & built assets
COPY --from=vendor /app /var/www/html
COPY --from=frontend /app/public/build /var/www/html/public/build

# Permissions for Laravel
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data /var/www/html

# FPM listens on 9000
EXPOSE 9000
CMD ["php-fpm", "-F"]