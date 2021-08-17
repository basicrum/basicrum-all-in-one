<?php
if (isset($_ENV['BOOTSTRAP_RESET_DATABASE']) && $_ENV['BOOTSTRAP_RESET_DATABASE'] == true) {
    echo "Resetting test database...";
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:drop --env=test --force --no-interaction',
        __DIR__
    ));
    passthru(sprintf(
        'php "%s/../bin/console" doctrine:database:create --env=test --no-interaction',
        __DIR__
    ));

    passthru(sprintf(
        'php "%s/../bin/console" doctrine:schema:create --env=test --no-interaction',
        __DIR__
    ));
    echo " Done" . PHP_EOL . PHP_EOL;
}

require __DIR__.'/../vendor/autoload.php';