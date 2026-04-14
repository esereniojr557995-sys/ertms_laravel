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

# Copy project files
COPY . .

# FIX 1: Create .env.example if missing, then copy to .env for build-time use
RUN if [ ! -f .env.example ]; then \
    printf 'APP_NAME=ERTMS\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=ertms\nDB_USERNAME=root\nDB_PASSWORD=\nSESSION_DRIVER=file\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n' > .env.example; \
fi
RUN cp .env.example .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generate temporary app key for build-time artisan commands
RUN php artisan key:generate --force

# FIX 2: Use npm install if package-lock.json is missing (npm ci requires it)
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
RUN npm run build

# Create all required Laravel storage directories
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/app/public \
             storage/logs \
             bootstrap/cache \
             public/uploads

# FIX 3: Own the entire project directory so www-data can write to storage
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# Create public storage symlink
RUN php artisan storage:link || true

# Clear all caches — real env vars from Render override .env at runtime
RUN php artisan config:clear \
 && php artisan view:clear \
 && php artisan route:clear \
 && php artisan cache:clear

EXPOSE 10000

# FIX 4: Use a proper bash startup script with set -e so errors are visible
RUN printf '#!/bin/bash\nset -e\necho "[ERTMS] Caching config..."\nphp artisan config:cache\necho "[ERTMS] Caching routes..."\nphp artisan route:cache\necho "[ERTMS] Caching views..."\nphp artisan view:cache\necho "[ERTMS] Running migrations..."\nphp artisan migrate --force\necho "[ERTMS] Starting Apache..."\nexec apache2-foreground\n' > /start.sh \
 && chmod +x /start.sh

CMD ["/start.sh"]