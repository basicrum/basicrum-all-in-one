ARG PHP_VERSION="7.3"

FROM php:${PHP_VERSION}-cli-alpine

# Install php extennsions (mysql & zip)
RUN apk add --no-cache zip bash libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo_mysql zip  \
    && apk del --purge .build-deps

# Configure crons
COPY crontab /etc/crontabs/basicrum
RUN crontab /etc/crontabs/basicrum

ENTRYPOINT []
CMD ["crond", "-f", "-L", "/dev/stderr"]

