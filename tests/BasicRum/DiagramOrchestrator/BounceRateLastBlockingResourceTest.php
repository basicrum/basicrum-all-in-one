<?php

namespace  App\Tests\BasicRum\DiagramOrchestrator;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\CollaboratorsAggregator;
use App\BasicRum\DiagramOrchestrator;

class BounceRateLastBlockingResourceTest extends FixturesTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testBounceRateLastBlockingResourceSelect()
    {
        $requirementsArr = [
            'periods' => [
                [
                    'from_date' => '10/24/2018',
                    'to_date'   => '10/24/2018'
                ]
            ],
            'technical_metrics' => [
                'last_blocking_resource' => 1
            ],
            'business_metrics'  => [
                'bounce_rate'       => 1
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
                                'time'            => 344,
                                'pageViewsCount'  => 1,
                                'guid'            => 'ffe7ccec-c9d1-410c-a189-c166edee257f_1549190244901'
                            ]
                        ]
                ]
            ],
            $res
        );
    }

}