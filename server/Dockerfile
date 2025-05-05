FROM bitnami/laravel

WORKDIR /app

COPY . /app/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install_packages php-pgsql php8.2-pgsql

RUN composer install --no-dev --optimize-autoloader \
    && php artisan key:generate \
    && php artisan config:cache \
    && php artisan migrate --force \
    && php artisan optimize:clear