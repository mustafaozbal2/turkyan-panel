FROM php:8.2-apache

# Gerekli paketleri yükle
RUN apt-get update && apt-get install -y \
    git zip unzip curl libzip-dev libpng-dev libonig-dev libxml2-dev npm \
    && docker-php-ext-install pdo_mysql zip

# Composer yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel app dosyaları
COPY . /var/www/html/

# Apache config
RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite

EXPOSE 80
