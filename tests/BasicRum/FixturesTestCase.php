<?php

namespace App\Tests\BasicRum;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Command\CacheCleanCommand;

class FixturesTestCase extends KernelTestCase
{

    use RefreshDatabaseTrait;

    protected function setUp()
    {
        $cacheCommand = new CacheCleanCommand();
        $cacheCommand->clearCache();

        static::bootKernel();
    }

}