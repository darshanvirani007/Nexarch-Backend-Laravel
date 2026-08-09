FROM composer:2 AS vendor
WORKDIR /app
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.3-cli-alpine
RUN apk add --no-cache libpq icu-libs oniguruma openssl \
    && apk add --no-cache --virtual .build-deps libpq-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo_pgsql intl mbstring \
    && apk del .build-deps
WORKDIR /app
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chmod +x docker/start.sh \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
EXPOSE 10000
CMD ["docker/start.sh"]
