<?php

namespace App\Tests\BasicRum\DiagramOrchestrator;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;

class CountPageViewsTest extends KernelTestCase
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

    public function testCountPageViews()
    {
        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => 'mobile'
                ]
            ],
            'periods' => [
                [
                    'from_date' => '10/24/2018',
                    'to_date'   => '10/24/2018'
                ]
            ],
            'business_metrics'  => [
                'page_views_count' => 1,
            ]
        ];

        $collaboratorsAggregator = new CollaboratorsAggregator();

        $collaboratorsAggregator->fillRequirements($requirementsArr);

        $diagramOrchestrator = new DiagramOrchestrator(
            $collaboratorsAggregator->getCollaborators(),
            $this->_getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        $this->assertEquals(
            [
                [
                    '2018-10-24 00:00:00' =>
                        [
                            [
                                'count' => '1'
                            ]
                        ]
                ]
            ],
            $res
        );
    }

}