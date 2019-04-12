#!/usr/bin/env sh
set -e

[ ! -d '/app/vendor' ] && composer update symfony/flex --no-plugins && composer install --optimize-autoloader --no-interaction