<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\TimeToFirstByte;

use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

class FirstByteFilterTest extends FixturesTestCase
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
    public function testBytePaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
            'is',
            '150'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$firstByte],
            $flavor
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [
                        'data_rows' =>[
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
    public function testFirstByteNotFound()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
            'is',
            '91999991'
        );

        $flavor = new DataRows('navigation_timings', ['page_view_id']);

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$firstByte],
            $flavor
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [
                        'data_rows' =>[

                        ]
                    ]
            ],
            $res
        );
    }

}