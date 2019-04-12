FROM php:7.3.4-fpm-alpine

WORKDIR /usr/src/app

# install bash and php extensions
RUN apk add --update bash \
    && docker-php-ext-install pdo_mysql

# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
    && php -r "unlink('composer-setup.php');"
