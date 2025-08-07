FROM php:8.2-apache

# Apache için rewrite modülünü aktif et
RUN a2enmod rewrite

# Laravel için gerekli PHP eklentileri
RUN docker-php-ext-install pdo pdo_mysql

# Laravel dosyalarını kopyala
COPY . /var/www/html

# Apache için doğru root dizin ve izinleri ayarla
WORKDIR /var/www/html

# Laravel public klasörünü Apache'nin root'u olarak ayarla
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# public klasörüne erişim izni ver
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# .env, storage izinleri vs.
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
