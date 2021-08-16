ARG PHP_VERSION="8.0.9"

FROM php:${PHP_VERSION}-cli-alpine

# Install php extennsions (zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip  \
    && apk del --purge .build-deps

RUN apk add --no-cache dcron libcap && \
    chown www-data:www-data /usr/sbin/crond && \
    setcap cap_setgid=ep /usr/sbin/crond

# Configure crons
COPY --chown=www-data:www-data crontab /home/www-data/crontabs/www-data

USER www-data
COPY docker-entrypoint.sh /
ENTRYPOINT ["/docker-entrypoint.sh"]