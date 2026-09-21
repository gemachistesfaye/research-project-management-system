#!/bin/sh

# Ensure SQLite database exists
touch /var/www/database/database.sqlite
chown www-data:www-data /var/www/database/database.sqlite

# Run Laravel migrations (force for production)
php /var/www/artisan migrate --force

# Optimize Laravel for production
php /var/www/artisan config:cache
php /var/www/artisan route:cache
php /var/www/artisan view:cache

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"

