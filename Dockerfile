FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /var/www/html/uploads \
    && chmod -R 777 /var/www/html/uploads
