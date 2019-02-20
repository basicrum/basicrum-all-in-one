<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\BasicRum\Layers\DataLayer;
use App\BasicRum\Periods\Period;
use App\BasicRum\Filters\Secondary\Url;

class UrlLikeTest extends KernelTestCase
{

    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testUrlLikeFound()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $url = new Url(
            'contains',
            'https://www.basicrum.com/contact'
        );

        $dataLayer = new DataLayer(
            $this->_getDoctrine(),
            $period,
            [$url]
        );

        $res = $dataLayer->process();

        $this->assertEquals(
            [
                [
                    [
                        'pageViewId' => 2,

                    ]
                ]
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
//        $dataLayer = new DataLayer(
//            $this->_getDoctrine(),
//            $period,
//            [$url]
//        );
//
//        $res = $dataLayer->process();
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