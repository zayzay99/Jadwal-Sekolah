#!/bin/sh

# FIX KRITIS NGINX PORT: Ganti port 8080 di nginx.conf dengan $PORT Railway.
# Ini memastikan Nginx mendengarkan pada port yang benar.
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/conf.d/default.conf

# Jalankan Migrasi Database
# (Penting untuk menghindari crash jika tabel belum ada)
/usr/bin/php /var/www/artisan migrate --force

# Start Supervisor (yang akan menjalankan Nginx dan PHP-FPM)
/usr/bin/supervisord -c /etc/supervisord.conf