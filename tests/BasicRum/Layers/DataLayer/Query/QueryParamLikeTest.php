<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Secondary\QueryParam;

class QueryParamLikeTest extends FixturesTestCase
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
    public function testQueryParamLikeFound()
    {
        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

        $queryParam = new QueryParam(
            'contains',
            'nenineni'
        );

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$queryParam]
        );

        $res = $dataLayer->process();

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

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$queryParam]
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