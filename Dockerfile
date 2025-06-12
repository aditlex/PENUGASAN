# --- PHP & Apache base ---
FROM php:8.2-apache

# --- Install system dependencies ---
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libpq-dev libjpeg-dev libfreetype6-dev \
    gnupg ca-certificates && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo_pgsql mbstring zip gd && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# --- Install Composer ---
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- Install Node.js & npm (for Vite) ---
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# --- Set working directory ---
WORKDIR /var/www/html

# --- Copy Laravel project ---
COPY . .

# --- Copy Apache config ---
COPY conf/apache/laravel.conf /etc/apache2/sites-available/000-default.conf

# --- Set permissions ---
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# --- Enable Apache rewrite ---
RUN a2enmod rewrite

# --- Install PHP dependencies ---
RUN composer install --optimize-autoloader --no-dev

# --- Install Node dependencies & build Vite ---
RUN npm install && \
    npm run build

# --- Laravel setup ---
RUN cp .env.example .env && \
    php artisan config:clear && \
    php artisan key:generate

EXPOSE 80

CMD ["apache2-foreground"]
