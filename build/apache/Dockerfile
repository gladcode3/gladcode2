FROM php:8.1.5-apache
RUN apt update && apt install -y zlib1g-dev libpng-dev libzip-dev zip unzip
RUN docker-php-ext-install gd pdo pdo_mysql exif
RUN a2enmod rewrite
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY ./php.ini /usr/local/etc/php
WORKDIR /var/www/html
# COPY ./composer.json .
# RUN composer update
EXPOSE 80
