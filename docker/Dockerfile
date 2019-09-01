ARG BASEIMAGE="tianon/true"
ARG BASEDIR=".."
FROM composer:latest as composer-builder

WORKDIR /var/www

COPY ${BASEDIR}/* /var/www/

ARG APP_ENV=prod

RUN set -xe \
    && if [ "$APP_ENV" = "prod" ]; then export ARGS="--no-dev"; fi \
    && composer install --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --apcu-autoloader $ARGS

RUN composer dump-autoload --classmap-authoritative

FROM ${BASEIMAGE}

COPY --from=composer-builder /var/www /

VOLUME /usr/src/app