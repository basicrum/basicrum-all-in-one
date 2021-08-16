#!/bin/sh
set -e

cp docker/symfony_app/.env .env

composer install --no-interaction;