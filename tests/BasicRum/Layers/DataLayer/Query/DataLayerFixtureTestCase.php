<?php namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Layers\DataLayer;
use App\Tests\BasicRum\FixturesTestCase;

class DataLayerFixtureTestCase extends FixturesTestCase
{

    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return DataLayer|object|null
     */
    protected function getDataLayer()
    {
        return self::bootKernel()->getContainer()->get(DataLayer::class);
    }

}
