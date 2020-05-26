<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Secondary\Url;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class UrlLikeTest extends DataLayerFixtureTestCase
{
    /**
     * @group data_query
     */
    public function testUrlLikeFound()
    {
        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

        $url = new Url(
            'contains',
            'https://www.basicrum.com/contact'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $res = $this->getDataLayer()->load(
            $period,
            [$url],
            $flavor
        )->process();

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' =>
                    [
                        'data_rows' => [
                            [
                                'page_view_id' => 3,
                            ],
                        ],
                    ],
            ],
            $res
        );
    }

//    public function testUrlLikeNotFound()
//    {
//        $period = new Period();
//        $period->setPeriod('10/24/2018', '10/24/2018');
//
//        $url = new Url(
//            'contains',
//            'https://www.basicrum.com/doesnotexist-url'
//        );
//
//        $res = $this->getDataLayer()->load(
//            $period,
//            [$url],
//            $flavor
//        )->process();
//
//        $this->assertEquals(
//            [
//                [
//                    [
//                        'pageViewId' => 2
//                    ]
//                ]
//            ],
//            $res
//        );
//    }

}
