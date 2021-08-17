#!/usr/bin/env bash
set -e

[ ! -d '/app/vendor' ] && composer install --optimize-autoloader --no-interaction