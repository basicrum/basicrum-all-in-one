<?php

namespace App\Tests\BasicRum\DiagramOrchestrator;

use App\Tests\BasicRum\FixturesTestCase;

use App\BasicRum\DiagramOrchestrator;

class CountPageViewsTest extends FixturesTestCase
{

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testCountPageViews()
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
                        'name' => 'Page Views',
                        'color' => '#ff0000'
                    ],
                    'data_requirements' => [
                        'business_metrics' => [
                            'page_views_count' => 1
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
                                'count' => '1'
                            ]
                        ]
                ]
            ],
            $res
        );
    }

}