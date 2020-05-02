#!/bin/sh
set -e

if [ ! -f ".env" ]; then
  cp docker/symfony_app/.env .env
fi

composer install --no-interaction;

while [ -z "$($PWD/bin/console doctrine:migrations:migrate --no-interaction)" ]; do
 echo 'Waiting for MySql to start ...' && sleep 2
done

echo 'Migration completed!'
