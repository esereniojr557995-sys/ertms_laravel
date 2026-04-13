FROM php:8.2-apache

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip mbstring xml gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set Render's required port (10000)
RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

# Point document root to Laravel's public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Allow .htaccess overrides for Laravel routing
RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy the full Laravel project
COPY . .

# Create a temporary .env from the example so Composer/Artisan can run
# Real environment variables are injected by Render at runtime
RUN cp .env.example .env

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generate a temporary app key so artisan commands work during build
RUN php artisan key:generate

# Install frontend dependencies and compile Vite assets
RUN npm ci --prefer-offline
RUN npm run build

# Create required storage directories and set permissions
RUN mkdir -p storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/app/public \
             bootstrap/cache \
             public/uploads \
 && chown -R www-data:www-data storage bootstrap/cache public/uploads \
 && chmod -R 775 storage bootstrap/cache public/uploads

# Create the public storage symlink
RUN php artisan storage:link || true

# Clear config cache — real env vars will be loaded at runtime
RUN php artisan config:clear \
 && php artisan view:clear \
 && php artisan route:clear

EXPOSE 10000

# Use a startup script so we can run migrations + cache at container start
CMD php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan migrate --force \
 && apache2-foreground