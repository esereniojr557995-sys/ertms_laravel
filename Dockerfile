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

RUN a2enmod rewrite

RUN sed -i 's/Listen 80/Listen 10000/g' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/g' /etc/apache2/sites-available/000-default.conf

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
 && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

RUN printf '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/laravel.conf \
 && a2enconf laravel

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Create .env for build-time artisan commands
RUN if [ ! -f .env.example ]; then \
    printf 'APP_NAME=ERTMS\nAPP_ENV=production\nAPP_KEY=\nAPP_DEBUG=false\nAPP_URL=http://localhost\nDB_CONNECTION=mysql\nDB_HOST=127.0.0.1\nDB_PORT=3306\nDB_DATABASE=ertms\nDB_USERNAME=root\nDB_PASSWORD=\nSESSION_DRIVER=file\nCACHE_STORE=file\nQUEUE_CONNECTION=sync\n' > .env.example; \
fi
RUN cp .env.example .env

# ── FIX: Create bootstrap/cache BEFORE composer install ──────────────────────
RUN mkdir -p bootstrap/cache \
 && chmod -R 777 bootstrap/cache

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generate temporary app key
RUN php artisan key:generate --force

# Install and build frontend assets
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
RUN npm run build

# Create all required Laravel directories and set permissions
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/app/public \
             storage/logs \
             public/uploads \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html \
 && chmod -R 775 storage bootstrap/cache

# Storage symlink
RUN php artisan storage:link || true

# Clear caches — real env vars come from Render at runtime
RUN php artisan config:clear \
 && php artisan view:clear \
 && php artisan route:clear \
 && php artisan cache:clear

EXPOSE 10000

# Startup script: rebuild caches with real env vars, migrate, start Apache
RUN printf '#!/bin/bash\nset -e\necho "[ERTMS] Caching config..."\nphp artisan config:cache\necho "[ERTMS] Caching routes..."\nphp artisan route:cache\necho "[ERTMS] Caching views..."\nphp artisan view:cache\necho "[ERTMS] Running migrations..."\nphp artisan migrate --force\necho "[ERTMS] Starting Apache..."\nexec apache2-foreground\n' > /start.sh \
 && chmod +x /start.sh

CMD ["/start.sh"]