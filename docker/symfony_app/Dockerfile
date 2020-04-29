ARG PHP_VERSION="7.3"

FROM php:${PHP_VERSION}-fpm-alpine

WORKDIR /usr/src/app

# Install php extennsions (mysql & zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo_mysql zip  \
    && apk del --purge .build-deps

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
    && php -r "unlink('composer-setup.php');"

# https://github.com/moby/moby/issues/2259
RUN mkdir -p /usr/src/app && chown -R www-data:www-data /usr/src/app
