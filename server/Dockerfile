FROM bitnami/laravel

WORKDIR /app

COPY . /app/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install_packages php8.2-pgsql

RUN echo "extension=pgsql.so" >> /opt/bitnami/php/etc/php.ini && \
    echo "extension=pdo_pgsql.so" >> /opt/bitnami/php/etc/php.ini

RUN composer install --no-dev --optimize-autoloader

CMD ["bash", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]