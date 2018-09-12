#!/usr/bin/env sh
set -e

[ ! -d '/app/node_modules' ] && npm install
[ ! -d '/app/vendor' ] && composer update symfony/flex --no-plugins && composer install --optimize-autoloader --no-interaction

php bin/console server:run *:80
