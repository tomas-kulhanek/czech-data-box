FROM composer:2 AS composer

FROM ghcr.io/tomas-kulhanek/docker-application:main AS builder
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends install git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./composer.* ./
RUN composer install --no-dev --no-progress --no-interaction --no-scripts


FROM ghcr.io/tomas-kulhanek/docker-application:main AS development
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends upgrade && \
    apt-get -y --no-install-recommends install git vim curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists

COPY --from=composer /usr/bin/composer /usr/bin/composer


FROM ghcr.io/tomas-kulhanek/docker-application:main
WORKDIR /var/www
RUN apt-get -y --no-install-recommends update && \
    apt-get -y --no-install-recommends upgrade && \
    apt-get -y --no-install-recommends install curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /var/cache/apt/lists
COPY --from=builder /var/www .
COPY . ./
