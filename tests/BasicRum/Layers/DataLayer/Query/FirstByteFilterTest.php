<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\CommonTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\TimeToFirstByte;

class FirstByteFilterTest extends CommonTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testBytePaintEqualsTo()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
            'is',
            '150'
        );

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$firstByte]
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [
                        [
                            'pageViewId' => 1
                        ]
                    ]
            ],
            $res
        );
    }

    public function testFirstByteNotFound()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $firstByte = new TimeToFirstByte(
            'is',
            '91999991'
        );

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$firstByte]
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' =>
                    [

                    ]
            ],
            $res
        );
    }

}