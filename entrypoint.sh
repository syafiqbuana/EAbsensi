#!/bin/sh

# Set Apache port dynamically from Render's $PORT environment variable, default to 80
PORT=${PORT:-80}
sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Cache Laravel configurations for production
php artisan optimize
php artisan view:cache
php artisan event:cache

# Run database migrations
php artisan migrate --force

# Start Apache in foreground
exec apache2-foreground
