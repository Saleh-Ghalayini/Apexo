FROM bitnami/laravel

WORKDIR /app

COPY . /app/
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN cp .env.example .env \
    && composer install --no-dev --optimize-autoloader \
    && php artisan key:generate \
    && php artisan config:cache \
    && php artisan migrate --force

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
