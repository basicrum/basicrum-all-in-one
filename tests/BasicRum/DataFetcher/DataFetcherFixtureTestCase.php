<?php

namespace App\Tests\BasicRum\DataFetcher;

use App\BasicRum\DataFetcher;
use App\Tests\BasicRum\FixturesTestCase;

class DataFetcherFixtureTestCase extends FixturesTestCase
{

    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return DataFetcher|object|null
     */
    protected function getDataLayer()
    {
        return self::bootKernel()->getContainer()->get(DataFetcher::class);
    }

}
