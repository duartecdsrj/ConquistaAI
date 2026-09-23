FROM composer:2 AS dependencies
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php:8.3-cli-alpine
RUN docker-php-ext-install pdo_mysql
WORKDIR /app
COPY --from=dependencies /app/vendor ./vendor
COPY . .
EXPOSE 8000
CMD ["sh", "-c", "php bin/migrate.php && php -S 0.0.0.0:8000 -t public public/router.php"]
