FROM php:8.2-apache

# Apache rewrite modülü
RUN a2enmod rewrite

# Sistem ve PHP paketleri
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Composer kur
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projeyi kopyala
COPY . /var/www/html

# Çalışma dizini
WORKDIR /var/www/html

# Apache public dizini ayarla
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# .env yoksa örnekten oluştur
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Laravel izin ayarları
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Laravel bağımlılıkları yükle
RUN composer install --no-dev --optimize-autoloader

# Laravel cache ve config işlemleri (hata engelleyici ile)
RUN php artisan key:generate || true \
    && php artisan config:clear || true \
    && php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

CMD ["apache2-foreground"]
