#!/bin/bash

set -e

# Override .env with production values (the committed .env has Windows paths)
export DB_CONNECTION=sqlite
export DB_DATABASE=/data/database.sqlite
export CACHE_DRIVER=file
export SESSION_DRIVER=file
export APP_KEY=base64:d19mTWFnZWMxMjM0NTY3ODkwMTIzNDU2Nzg5MDEyMzQ=
export APP_DEBUG=false

# Create SQLite database if not exists
mkdir -p /data

# Remove stale database to force clean reseed (demo app, no production data)
rm -f /data/database.sqlite
touch /data/database.sqlite

# Run migrations
php artisan migrate --force

# Seed if users table is empty or missing
php artisan tinker --execute="
\$exists = Schema::hasTable('users');
\$count = \$exists ? DB::table('users')->count() : 0;
if (\$count == 0) {
    Artisan::call('db:seed');
    echo 'SEED_OK';
} else {
    echo 'USERS_EXIST (' . \$count . ')';
}
" 2>&1 || echo "SEED_FAILED"

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
