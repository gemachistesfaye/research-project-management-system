#!/bin/bash

# Create SQLite database if not exists
touch /data/database.sqlite

# Link database to persistent storage
ln -sf /data/database.sqlite database/database.sqlite

# Run migrations
php artisan migrate --force

# Seed only if tables are empty
php artisan tinker --execute="if(DB::table('users')->count() == 0){ Artisan::call('db:seed'); echo Artisan::output(); }" 2>/dev/null || true

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start the server
exec php artisan serve --host=0.0.0.0 --port=8000
