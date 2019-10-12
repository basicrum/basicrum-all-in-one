<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Layers\DataLayer\Query\MainDataSelect\DataRows;

//use App\BasicRum\Filters\Primary\TimeToFirstPaint;
//use App\BasicRum\TechnicalMetrics\TimeToFirstPaint;

class FirstPaintSelectTest extends FixturesTestCase
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
    public function testFirstPaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstPaintFilter = new \App\BasicRum\Filters\Primary\TimeToFirstPaint(
            'is',
            '344'
        );

        $firstPaintSelect = new \App\BasicRum\TechnicalMetrics\TimeToFirstPaint();

        $flavor = new DataRows('navigation_timings', ['page_view_id', 'first_paint']);

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$firstPaintFilter, $firstPaintSelect],
            $flavor
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [
                        'data_rows' => [
                            [
                                'page_view_id' => 1,
                                'first_paint' => 344
                            ]
                        ]
                    ]
            ],
            $res
        );
    }

}