# FROM php:8.2-fpm

# # --- STAGE 1: Dependency Installation & PHP Extensions ---
# # libpng-dev diperlukan untuk instalasi GD.
# # Menambahkan 'gd' ke daftar ekstensi PHP.
# RUN apt-get update && apt-get install -y \
#     libzip-dev unzip curl git libpng-dev libonig-dev libxml2-dev \
#     && docker-php-ext-install zip pdo pdo_mysql mbstring bcmath gd

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www

# # Copy project files
# COPY . .

# # --- FIX: Update dependencies to ensure PHP 8.2 compatibility ---
# # Menggunakan 'composer update' untuk menghasilkan composer.lock yang kompatibel dengan PHP 8.2.
# # Ini menyelesaikan error "Your lock file does not contain a compatible set of packages."
# RUN composer update --no-dev --optimize-autoloader

# # --- STAGE 2: Setup & Final Configuration ---
# # Set permissions
# RUN chown -R www-data:www-data /var/www \
#     && chmod -R 775 /var/www/storage

# # Expose port
# EXPOSE 8000

# # --- FIX: Using dynamic $PORT for Railway deployment ---
# # Menggunakan ${PORT:-8000} agar aplikasi mendengarkan pada port yang disediakan Railway (yang mungkin bukan 8000).
# # Ini penting untuk menghindari error 502 Bad Gateway.
# CMD php artisan serve --host=0.0.0.0 --port=8000
# # CMD php -S 0.0.0.0:${PORT:-8000} -t public/index.php




# NEWW
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

# Install Nginx, Supervisor, and Bash (Bash penting untuk entrypoint script)
RUN apk add --no-cache nginx supervisor bash

# Copy PHP extensions/binaries from the builder stage
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/etc/php-fpm.d/ /usr/local/etc/php-fpm.d/
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

# Copy application code
WORKDIR /var/www
COPY --from=builder /var/www /var/www

# Configure Nginx, FPM, dan Supervisor
COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

# Tambahkan Pembersihan Cache di Build Time (Penting!)
RUN php /var/www/artisan config:clear && php /var/www/artisan view:clear

# Expose port
EXPOSE 8080

# Masukkan Skrip Startup yang menangani port dinamis dan migrasi
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Perintah untuk menjalankan container
CMD ["/usr/local/bin/entrypoint.sh"]