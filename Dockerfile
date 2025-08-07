FROM php:8.2-apache

# Apache modları
RUN a2enmod rewrite

# Sistem paketleri ve PHP eklentileri
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer kurulumu
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projeyi kopyala
COPY . /var/www/html

# Çalışma dizini
WORKDIR /var/www/html

# Apache'nin root'u Laravel'in public dizini olmalı
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Laravel klasör izinleri
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Laravel bağımlılıklarını yükle
RUN composer install --no-dev --optimize-autoloader

# Laravel config
RUN php artisan key:generate \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

CMD ["apache2-foreground"]
