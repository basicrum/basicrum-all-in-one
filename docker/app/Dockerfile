FROM php:7.2-alpine

VOLUME /app
WORKDIR /app

# install node+npm and some php extensions
RUN apk add --update nodejs nodejs-npm $PHPIZE_DEPS \
    && docker-php-ext-install pcntl pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug


# install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --filename=composer --install-dir=/usr/local/bin \
    && php -r "unlink('composer-setup.php');"

COPY ./docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
