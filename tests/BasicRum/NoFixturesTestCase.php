<?php

namespace App\Tests\BasicRum;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Command\CacheCleanCommand;

class NoFixturesTestCase extends KernelTestCase
{

    protected function setUp()
    {
        $cacheCommand = new CacheCleanCommand();
        $cacheCommand->clearCache();

        static::bootKernel();
    }

}