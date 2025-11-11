FROM php:8.2-fpm

# --- STAGE 1: Dependency Installation & PHP Extensions ---
# libpng-dev diperlukan untuk instalasi GD.
# Menambahkan 'gd' ke daftar ekstensi PHP.
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install zip pdo pdo_mysql mbstring bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# --- FIX: Update dependencies to ensure PHP 8.2 compatibility ---
# Menggunakan 'composer update' untuk menghasilkan composer.lock yang kompatibel dengan PHP 8.2.
# Ini menyelesaikan error "Your lock file does not contain a compatible set of packages."
RUN composer update --no-dev --optimize-autoloader

# --- STAGE 2: Setup & Final Configuration ---
# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage

# Expose port
EXPOSE 8000

# --- FIX: Using dynamic $PORT for Railway deployment ---
# Menggunakan ${PORT:-8000} agar aplikasi mendengarkan pada port yang disediakan Railway (yang mungkin bukan 8000).
# Ini penting untuk menghindari error 502 Bad Gateway.
# CMD php artisan serve --host=0.0.0.0 --port=8000
CMD php -S 0.0.0.0:${PORT:-8000} -t public/index.php