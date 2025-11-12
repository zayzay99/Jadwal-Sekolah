FROM php:8.2-fpm

# Install system dependencies & extensions
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git libpng-dev libonig-dev libxml2-dev libwebp-dev \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip pdo pdo_mysql mbstring bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# --- OPTIMASI CACHE & ARTISAN FIX ---

# 1. Copy hanya file Composer
COPY composer.json composer.lock ./

# 2. Instal dependencies (Menginstal SEMUA package, termasuk maatwebsite/excel)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 3. Copy sisa project (Termasuk file artisan)
COPY . .

# 4. Jalankan script pasca-instalasi secara manual SETELAH artisan ada
RUN php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Start server
CMD php artisan serve --host=0.0.0.0 --port=${PORT}