#!/bin/sh

while [ -z "$($PWD/bin/console doctrine:migrations:migrate --no-interaction)" ]; do
 echo 'Waiting for MySql to start ...' && sleep 2
done

echo 'Migration completed!'
