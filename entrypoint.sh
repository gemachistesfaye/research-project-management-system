#!/bin/bash

set -e

# Create SQLite database if not exists
mkdir -p /data
touch /data/database.sqlite

# Run migrations
php artisan migrate --force

# Seed only if tables are empty
php artisan tinker --execute="if(DB::table('users')->count() == 0){ Artisan::call('db:seed'); echo Artisan::output(); echo 'SEED_OK'; } else { echo 'USERS_EXIST'; }" 2>&1 || echo "SEED_SKIPPED"

# Clear stale caches and rate limiter before rebuilding
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the server
exec php artisan serve --host=0.0.0.0 --port=8000
