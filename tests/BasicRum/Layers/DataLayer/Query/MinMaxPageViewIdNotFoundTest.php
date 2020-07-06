<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Filters\Primary\DeviceType;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;
use App\BasicRum\Periods\Period;

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

        $flavor = new DataRows('rum_data_flat', ['rum_data_id']);

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
}
