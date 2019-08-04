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
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane'
                ],
                'data_requirements' => [
                    'period' => [
                        'type'  => 'fixed',
                        'start' => '10/24/2018',
                        'end'   => '10/24/2018'
                    ],
                    'filters' => [
                        'device_type' => [
                            'condition'    => 'is',
                            'search_value' => '2'
                        ]
                    ]
                ]
            ],
            'segments' => [
                1 => [
                    'presentation' => [
                        'name' => 'Sessions',
                        'color' => '#ff0000'
                    ],
                    'group_data' => 'bounce_rate',
                    'data_requirements' => [
                        'technical_metrics' => [
                            'time_to_first_paint' => 1
                        ]
                    ]
                ],
                2 => [
                    'presentation' => [
                        'name' => 'Bounce Rate',
                        'color' => '#000000'
                    ],
                    'group_data' => 'bounce_rate',
                    'data_requirements' => [

                        'business_metrics' => [
                            'bounce_rate' => 1,
                            'stay_on_page_time' => 1
                        ]
                    ]
                ]
            ]
        ];

        $diagramOrchestrator = new DiagramOrchestrator(
            $input,
            $this->_getDoctrine()
        );

        $res = $diagramOrchestrator->process();

        $this->assertEquals(
            [
                1 => [
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
                ],
                2 => [
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