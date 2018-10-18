#!/usr/bin/env sh
set -e

[ ! -d '/app/node_modules' ] && npm install
[ ! -d '/app/vendor' ] && composer update symfony/flex --no-plugins && composer install --optimize-autoloader --no-interaction

exec php bin/console server:run --no-interaction 0.0.0.0:80
