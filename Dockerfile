FROM php:8.2-fpm

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip pdo_sqlite

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Configure Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set up entrypoint
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]

