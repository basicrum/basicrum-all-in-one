#!/bin/sh
set -e

cat /etc/passwd

composer install --no-interaction;

while [ -z "$($PWD/bin/console doctrine:migrations:migrate --no-interaction)" ]; do
 echo 'Waiting for MySql to start ...' && sleep 2
done

echo 'Migration completed!'
