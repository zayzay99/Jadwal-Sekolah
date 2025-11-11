FROM php:8.2-fpm

# Instalasi dependencies sistem (libjpeg-dev, libfreetype-dev, dll.)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev unzip curl git libpng-dev libjpeg-dev libfreetype-dev libonig-dev libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# 1. Konfigurasi ekstensi GD dengan dukungan Freetype dan JPEG
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# 2. Instalasi ekstensi PHP lainnya
RUN docker-php-ext-install zip pdo pdo_mysql mbstring bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project
COPY . .

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Expose port
EXPOSE 8000

# Start server
CMD php artisan serve --host=0.0.0.0 --port=3306

# HAPUS BARIS INI:
# RUN docker-php-ext-install gd
# RUN composer install --no-dev --optimize-autoloader