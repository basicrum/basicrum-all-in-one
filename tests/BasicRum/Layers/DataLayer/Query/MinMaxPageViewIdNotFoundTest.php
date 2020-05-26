<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\DeviceType;

use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class MinMaxPageViewIdNotFoundTest extends DataLayerFixtureTestCase
{

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

        $res = $this->getDataLayer()->load(
            $period,
            [$deviceType],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '1986-05-15 00:00:00' => []
            ],
            $res
        );
    }

}
