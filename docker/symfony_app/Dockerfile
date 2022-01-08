ARG PHP_VERSION="8.0.9"

FROM php:${PHP_VERSION}-fpm-alpine

# Install php extennsions (zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip  \
    && apk del --purge .build-deps

# install composer
COPY --from=composer:2.1.5 /usr/bin/composer /usr/local/bin/composer
