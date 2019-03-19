<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use App\Tests\BasicRum\CommonTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Primary\DeviceType;

class MinMaxPageViewIdNotFound extends CommonTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testPageViewIdNotInRange()
    {
        $period = new Period();
        $period->setPeriod('05/15/1986', '05/15/1986');

        $deviceType = new DeviceType(
            'is',
            'mobile'
        );

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$deviceType]
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                '1986-05-15 00:00:00' => []
            ],
            $res
        );
    }

}