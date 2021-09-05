FROM composer:2 AS composer

FROM helppc/php-fpm:v2.0.1 AS builder
WORKDIR /build
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./composer.* ./
RUN composer install --no-dev --no-progress --no-interaction --no-scripts

FROM helppc/php-fpm-xdebug:v2.0.1
WORKDIR /var/www
COPY --from=builder /build .
COPY . ./
