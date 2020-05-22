<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Secondary\QueryParam;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class QueryParamLikeTest extends DataLayerFixtureTestCase
{
    /**
     * @group data_query
     */
    public function testQueryParamLikeFound()
    {
        $this->markTestSkipped('Fix query params query later.');

        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

        $queryParam = new QueryParam(
            'contains',
            'nenineni'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$queryParam],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' =>
                    [
                        [
                            'page_view_id' => 3,

                        ]
                    ]
            ],
            $res
        );
    }

    /**
     * @group data_query
     */
    public function testQueryParamLikeNotFound()
    {
        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

        $queryParam = new QueryParam(
            'contains',
            'nowaytofindme'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $dataLayer = $this->getDataLayer()->load(
            $period,
            [$queryParam],
            $flavor
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' => []
            ],
            $res
        );
    }

}
