#!/bin/bash

# Override .env with production values (the committed .env has Windows paths)
export DB_CONNECTION=sqlite
export DB_DATABASE=/data/database.sqlite
export CACHE_DRIVER=file
export SESSION_DRIVER=file
export APP_KEY=base64:d19mTWFnZWMxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ=
export APP_DEBUG=false

# Create SQLite database if not exists (preserve data on restarts and redeploys)
mkdir -p /data
if [ ! -f /data/database.sqlite ]; then
    touch /data/database.sqlite
    php artisan migrate --force
    php artisan db:seed --force 2>&1 || echo "SEED_FAILED"
else
    php artisan migrate --force
fi

# Clear stale caches and rate limiter before rebuilding
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link --force

# Start the server
exec php artisan serve --host=0.0.0.0 --port=8000
