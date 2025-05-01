FROM bitnami/laravel

WORKDIR /app

COPY ./server /app/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader
