<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

<<<<<<< HEAD
use App\BasicRum\Periods\Period;
=======
>>>>>>> 2ec8c91... navigation_timings to rum_data_flat
use App\BasicRum\Filters\Primary\DeviceType;
use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;
use App\BasicRum\Periods\Period;
use App\Tests\BasicRum\FixturesTestCase;

<<<<<<< HEAD
class MinMaxPageViewIdNotFoundTest extends DataLayerFixtureTestCase
=======
class MinMaxPageViewIdNotFoundTest extends FixturesTestCase
>>>>>>> 2ec8c91... navigation_timings to rum_data_flat
{
    /**
<<<<<<< HEAD
=======
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine(): \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
>>>>>>> 2ec8c91... navigation_timings to rum_data_flat
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

        $flavor = new DataRows('rum_data_flat', ['page_view_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$deviceType],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '1986-05-15 00:00:00' => [],
            ],
            $res
        );
    }
<<<<<<< HEAD

=======
>>>>>>> 2ec8c91... navigation_timings to rum_data_flat
}
