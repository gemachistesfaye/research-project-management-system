#!/bin/sh

# Ensure storage directories exist and have correct permissions
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/{cache,sessions,views}
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Determine the database path from environment or use default
DB_PATH="${DB_DATABASE:-/var/www/database/database.sqlite}"

# Ensure the directory for the database exists and is writable
DB_DIR="$(dirname "$DB_PATH")"
mkdir -p "$DB_DIR"
chown -R www-data:www-data "$DB_DIR"
chmod -R 775 "$DB_DIR"

# Create SQLite database file if it doesn't exist
touch "$DB_PATH"
chown www-data:www-data "$DB_PATH"
chmod 664 "$DB_PATH"

# Run Laravel migrations and seeders (force for production)
php /var/www/artisan migrate --force
php /var/www/artisan db:seed --force

# Optimize Laravel for production
php /var/www/artisan config:cache
php /var/www/artisan route:cache
php /var/www/artisan view:cache

# Ensure writable paths have correct permissions after cache commands
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache "$DB_DIR"

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
nginx -g "daemon off;"
