ARG PHP_VERSION="8.0.9"

FROM php:${PHP_VERSION}-fpm-alpine

# Install php extennsions (zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip  \
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
COPY --from=composer:2.1.5 /usr/bin/composer /usr/local/bin/composer
