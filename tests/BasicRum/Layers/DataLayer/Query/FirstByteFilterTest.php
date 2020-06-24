<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Filters\Primary\TimeToFirstByte;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;
use App\BasicRum\Periods\Period;

class FirstByteFilterTest extends DataLayerFixtureTestCase
{
    /**
     * @group data_query
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testBytePaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
            'is',
            '150'
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
    public function testFirstByteNotFound()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
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
