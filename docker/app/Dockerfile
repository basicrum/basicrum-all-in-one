FROM php:7.2-alpine

VOLUME /app
WORKDIR /app

# install node+npm and some php extensions
RUN apk add --update bash nodejs nodejs-npm $PHPIZE_DEPS \
    && docker-php-ext-install pcntl pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug


# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
    && php -r "unlink('composer-setup.php');"

COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
