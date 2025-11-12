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

# --- OPTIMASI CACHE DIMULAI DI SINI ---
# 1. Copy hanya file Composer
COPY composer.json composer.lock ./

# 2. Install dependencies (Langkah ini akan dicache jika lock file tidak berubah)
RUN composer install --no-dev --optimize-autoloader
RUN composer install maatwebsite/excel

# 3. Copy sisa project
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Start server
CMD php artisan serve --host=0.0.0.0 --port=${PORT}