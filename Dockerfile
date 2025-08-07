FROM php:8.2-apache

# Apache'de rewrite modülünü aktif et
RUN a2enmod rewrite

# Laravel dosyalarını kopyala
COPY . /var/www/html/

# public klasörünü Apache root olarak ayarla
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Apache'yi Laravel için ayarla
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Gerekli PHP uzantılarını yükle
RUN docker-php-ext-install pdo pdo_mysql

# Composer yükle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravel için permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

EXPOSE 80
