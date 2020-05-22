<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\DeviceType;

use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class DeviceTypeTest extends DataLayerFixtureTestCase
{

    /**
     * @group data_query
     */
    public function testDeviceTypeMobile()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $deviceType = new DeviceType(
            'is',
            '2'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$deviceType],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [
                        'data_rows' => [
                            [
                                'page_view_id' => 1
                            ]
                        ]
                    ]
            ],
            $res
        );
    }

    /**
     * @group data_query
     */
    public function testDeviceTypeDesktop()
    {
        $period = new Period();
        $period->setPeriod('10/25/2018', '10/25/2018');

        $deviceType = new DeviceType(
            'is',
            '1'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$deviceType],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-25 00:00:00' =>
                    [
                        'data_rows' => [
                            [
                                'page_view_id' => 2
                            ]
                        ]
                    ]
            ],
            $res
        );
    }
}
