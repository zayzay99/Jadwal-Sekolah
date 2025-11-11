# FROM php:8.2-fpm

# # Instalasi dependencies sistem (libjpeg-dev, libfreetype-dev, dll.)
# RUN apt-get update && apt-get install -y --no-install-recommends \
#     libzip-dev unzip curl git libpng-dev libjpeg-dev libfreetype-dev libonig-dev libxml2-dev \
#     && rm -rf /var/lib/apt/lists/*

# # 1. Konfigurasi ekstensi GD dengan dukungan Freetype dan JPEG
# RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
#     && docker-php-ext-install -j$(nproc) gd

# # 2. Instalasi ekstensi PHP lainnya
# RUN docker-php-ext-install zip pdo pdo_mysql mbstring bcmath

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www

# # Copy project
# COPY . .

# # Install Laravel dependencies
# RUN composer install --no-dev --optimize-autoloader

# # =======================================================
# # PERBAIKAN Izin Akses File UNTUK MENGHINDARI CRASH (502)
# # =======================================================
# RUN chown -R www-data:www-data /var/www \
#     # Pastikan 'storage' dan 'bootstrap/cache' bisa ditulis oleh user www-data
#     && chmod -R 775 /var/www/storage \
#     && chmod -R 775 /var/www/bootstrap/cache

# # Expose port (sudah benar, meski opsional)
# EXPOSE 8000

# COPY entrypoint.sh /usr/local/bin/entrypoint.sh
# RUN chmod +x /usr/local/bin/entrypoint.sh

# # Start server (sudah benar untuk Railway)
# ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# CMD ["php-fpm"]

FROM php:8.2-fpm

# Instalasi dependencies sistem (TERMASUK NGINX)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev unzip curl git libpng-dev libjpeg-dev libfreetype-dev libonig-dev libxml2-dev \
    # Tambahkan NGINX agar Railway bisa merutekan traffic
    nginx \ 
    && rm -rf /var/lib/apt/lists/*

# 1. Konfigurasi ekstensi GD
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

# =======================================================
# KONFIGURASI NGINX DAN PERMISSIONS
# =======================================================
# Copy Nginx configuration file
# Pastikan Anda sudah membuat file `nginx.conf` di root proyek Anda (Lihat penjelasan sebelumnya)
COPY nginx.conf /etc/nginx/sites-enabled/default

# Perbaiki izin akses file yang menjadi penyebab crash paling umum
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# =======================================================
# ENTRYPOINT (Menjalankan Nginx & PHP-FPM)
# =======================================================
# Buat entrypoint script untuk menjalankan kedua proses
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Jalankan entrypoint script yang akan memulai Nginx dan PHP-FPM
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["nginx"] # Nginx akan dijalankan sebagai proses utama, PHP-FPM dijalankan di entrypoint