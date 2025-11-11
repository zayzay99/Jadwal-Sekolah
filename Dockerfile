# --- STAGE 1: BUILD Dependencies and Run Composer ---
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies needed for both build and runtime
RUN apk add --no-cache git curl libzip-dev libpng-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql opcache bcmath zip gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory and copy files
WORKDIR /var/www
COPY . .

# Run Composer update for PHP 8.2 compatibility
RUN composer update --no-dev --optimize-autoloader

# --- STAGE 2: PRODUCTION Ready Image (Minimal) ---
FROM php:8.2-fpm-alpine AS final

# Install Nginx, Supervisor, dan Bash
RUN apk add --no-cache nginx supervisor bash

# --- FIX KRITIS: Instal Ekstensi PHP di Stage Final ---
# Ekstensi ini diperlukan oleh PHP-FPM untuk berjalan dan mengatasi error "unable to load dynamic library".
RUN apk add --no-cache libzip-dev libpng-dev libxml2-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql zip gd

# Copy PHP extensions/binaries from the builder stage
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/etc/php-fpm.d/ /usr/local/etc/php-fpm.d/

# Copy application code
WORKDIR /var/www
COPY --from=builder /var/www /var/www

# Configure Nginx, FPM, dan Supervisor
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

# Expose port
EXPOSE 8080

# Masukkan Skrip Startup yang menangani port dinamis dan migrasi
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Perintah untuk menjalankan container
CMD ["/usr/local/bin/entrypoint.sh"]