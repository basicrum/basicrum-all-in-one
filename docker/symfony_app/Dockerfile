FROM php:7.3.4-fpm-alpine

WORKDIR /usr/src/app

# install bash and php extensions
RUN apk add --update bash \
    && docker-php-ext-install pdo_mysql

# we need ZIP extension because some bundles depend on it and we can't complete composer install
RUN apk add --no-cache zip libzip-dev
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install zip

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
    && php -r "unlink('composer-setup.php');"