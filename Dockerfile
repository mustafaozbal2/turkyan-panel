FROM php:8.2-apache

# Apache rewrite modu aktif
RUN a2enmod rewrite

# Gerekli paketler ve PHP eklentileri
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Composer kur
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projeyi kopyala
COPY . /var/www/html

# Laravel çalışma dizini
WORKDIR /var/www/html

# Apache root'u "public" olarak ayarla
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Klasör izinleri
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# .env dosyasını kopyala (Eğer Secret Files kullandıysan bunu yorum satırı yap)
COPY .env /var/www/html/.env

# Laravel kurulum adımları
RUN composer install --no-dev --optimize-autoloader && \
    php artisan key:generate && \
    php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

CMD ["apache2-foreground"]
