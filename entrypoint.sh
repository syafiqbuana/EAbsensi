#!/bin/bash

# Pastikan script berhenti dan menampilkan error jika ada perintah yang gagal
set -e

echo "Memulai proses entrypoint..."

# 1. Pastikan struktur folder storage tersedia
echo "Membuat struktur folder storage..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# 2. Set permission yang benar untuk Apache (www-data)
echo "Mengatur hak akses direktori..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Cache Laravel (Pastikan file .env dari Railway sudah ter-inject dengan benar)
echo "Mengoptimalkan Laravel (Caching)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 4. Jalankan Migrasi Database
echo "Menjalankan migrasi database..."
# Catatan: Jika DB belum siap, script ini akan memaksanya. 
# '--force' wajib ada di production agar tidak ada prompt (y/n) yang menghentikan build.
php artisan migrate --force

echo "Setup selesai. Memulai Apache..."

# 5. Jalankan Apache di foreground (Wajib agar container tidak mati)
exec apache2-foreground