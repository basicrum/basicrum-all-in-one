ARG PHP_VERSION="7.3"

FROM php:${PHP_VERSION}-fpm-alpine

# Install php extennsions (mysql & zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo_mysql zip  \
    && apk del --purge .build-deps

# Install xdebug if required
ARG XDEBUG_ON="N"
RUN if [ "${XDEBUG_ON}" == "Y" ]; then \
    apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del --purge .phpize-deps; \
 fi

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
