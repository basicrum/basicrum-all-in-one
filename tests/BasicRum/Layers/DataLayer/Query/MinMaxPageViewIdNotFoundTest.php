<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\DeviceType;

use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class MinMaxPageViewIdNotFound extends FixturesTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * @group data_query
     */
    public function testPageViewIdNotInRange()
    {
        $period = new Period();
        $period->setPeriod('05/15/1986', '05/15/1986');

        $deviceType = new DeviceType(
            'is',
            'mobile'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$deviceType],
            $flavor
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '1986-05-15 00:00:00' => []
            ],
            $res
        );
    }

}