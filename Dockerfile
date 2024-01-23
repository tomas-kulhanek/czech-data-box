FROM composer:2 AS composer

FROM php:latest

RUN apt-get update \
     && apt-get install -y libzip-dev zlib1g-dev zlib1g-dev zip git libfcgi-bin jq librabbitmq-dev libpng-dev libonig-dev unzip \
     && apt-get clean \
     && rm -rf /var/lib/apt/list/* \
     && docker-php-ext-install pdo_mysql bcmath gd mysqli \
     && docker-php-ext-configure bcmath --enable-bcmath \
     && docker-php-ext-configure zip

COPY --from=composer /usr/bin/composer /usr/bin/composer