FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git libpng-dev libonig-dev libxml2-dev libwebp-dev \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip pdo pdo_mysql mbstring bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www


COPY composer.json composer.lock ./


RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .

RUN php artisan package:discover --ansi
RUN php artisan storage:link

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000

