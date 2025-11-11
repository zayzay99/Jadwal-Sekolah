#!/bin/bash

# Hentikan semua operasi jika ada error
set -e

# Berikan izin tulis penuh pada folder storage dan cache
# Ini adalah double check, mengalahkan masalah izin file yang bandel
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage

chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/bootstrap/cache

# Jika Anda ingin membersihkan cache setiap kali deploy (Opsional)
# php artisan optimize:clear 

# Jalankan PHP-FPM, bukan php artisan serve
exec php-fpm