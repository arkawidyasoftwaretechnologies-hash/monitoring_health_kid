#!/bin/sh

# Set correct permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Run composer if vendor doesn't exist (useful for development mounts)
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Run migrations
php artisan migrate --force

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start PHP-FPM
php-fpm
