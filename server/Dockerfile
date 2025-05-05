FROM composer:latest AS composer

WORKDIR /app
COPY . /app/

RUN composer install --no-dev --optimize-autoloader

FROM bitnami/laravel

WORKDIR /app

COPY --from=composer /app /app

RUN install_packages php-pgsql php8.2-pgsql

COPY .env.example /app/.env

ARG APP_KEY
ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_KEY=${APP_KEY}

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan optimize

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000