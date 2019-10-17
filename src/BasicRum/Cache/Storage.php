<?php

declare(strict_types=1);

namespace App\BasicRum\Cache;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class Storage
    extends FilesystemAdapter
{

    public function __construct(string $namespace = '', int $defaultLifetime = 0)
    {
        $projectDir = explode('/src/BasicRum',__DIR__)[0];
        $cacheDir = $projectDir . '/var/cache/' . $_ENV['APP_ENV'];

        parent::__construct($namespace, $defaultLifetime, $cacheDir);
    }

    /**
     * Used when we do local development or unit testing and we would like
     * to avoid getting pre-cached results
     *
     * @param string $cacheKey
     * @return bool
     */
    public function hasItem($cacheKey)
    {
        if (isset($_ENV['NO_CACHE']) && $_ENV['NO_CACHE'] == true) {
            return false;
        }

        return parent::hasItem($cacheKey);
    }

}