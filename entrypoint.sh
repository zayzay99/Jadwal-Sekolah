#!/bin/bash
set -e

# Selalu perbaiki izin storage di runtime
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Jalankan PHP-FPM
exec php-fpm