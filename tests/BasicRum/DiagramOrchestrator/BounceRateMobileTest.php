<?php

namespace  App\Tests\BasicRum\DiagramOrchestrator;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;

class BounceRateMobileTest extends FixturesTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testMobileFirstPaintBounceRateSelected()
    {
        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => '2'
                ]
            ],
            'periods' => [
                [
                    'from_date' => '10/24/2018',
                    'to_date'   => '10/24/2018'
                ]
            ],
            'technical_metrics' => [
                'time_to_first_paint' => 1
            ],
            'business_metrics'  => [
                'bounce_rate'       => 1,
                'stay_on_page_time' => 1
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
                                'pageViewId'      => 1,
                                'firstPageViewId' => 1,
                                'firstPaint'      => 344,
                                'pageViewsCount'  => 1,
                                'guid'            => 'first-closed-session',
                                'stayOnPageTime'  => 24
                            ]
                        ]
                ]
            ],
            $res
        );
    }

}