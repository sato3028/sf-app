FROM php:8.3-fpm

ARG PUID=1000
ARG PGID=1000
RUN groupmod -g $PGID www-data && \
    usermod -u $PUID -g www-data www-data

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    npm \
    nodejs \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
