```bash:Entrypoint Script:entrypoint.sh
#!/bin/sh

# 1. FIX KRITIS NGINX PORT: Ganti port 8080 di nginx.conf dengan $PORT Railway.
# Ini memastikan Nginx mendengarkan pada port yang benar.
sed -i "s/listen 8080;/listen ${PORT:-8080};/" /etc/nginx/conf.d/default.conf

# 2. Persiapan Laravel (Pembersihan Cache dan Migrasi)

# FIX: Bersihkan cache konfigurasi dan view saat runtime (PENTING)
/usr/bin/php /var/www/artisan config:clear
/usr/bin/php /var/www/artisan view:clear

# Jalankan Migrasi Database
/usr/bin/php /var/www/artisan migrate --force

# 3. Start Supervisor (yang akan menjalankan Nginx dan PHP-FPM)
/usr/bin/supervisord -c /etc/supervisord.conf
```eof