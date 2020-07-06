<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Filters\Primary\TimeToFirstPaint;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;
use App\BasicRum\Periods\Period;

class FirstPaintFilterTest extends DataLayerFixtureTestCase
{
    /**
     * @group data_query
     */
    public function testFirstPaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstPaint(
            'is',
            '344'
        );

        $flavor = new DataRows('rum_data_flat', ['rum_data_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$firstByte],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' => [
                    'data_rows' => [
                        [
                            'rum_data_id' => 1,
                        ],
                    ],
                ],
            ],
            $res
        );
    }

    /**
     * @group data_query
     */
    public function testFirstPaintNotFound()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/25/2018');

        $firstByte = new TimeToFirstPaint(
            'is',
            '91999991'
        );

        $flavor = new DataRows('rum_data_flat', ['rum_data_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$firstByte],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' => [
                    'data_rows' => [
                    ],
                ],
            ],
            $res
        );
    }
}
