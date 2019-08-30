<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use PHPUnit\Framework\TestCase;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;

class FirstByteAndBounceRateTest extends TestCase
{

    /**
     * @group diagram_builder
     */
    public function testBounceRateValuesInBuckets()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane'
                ],
                'data_requirements' => [
                    'period' => [
                        'type'  => 'moving',
                        'start' => '20',
                        'end'   => 'now',
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
                        'filters' => [
                            'device_type' => [
                                'condition'    => 'is',
                                'search_value' => '2'
                            ]
                        ],
                        'technical_metrics' => [
                            'time_to_first_byte' => 1
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
                            'bounce_rate' => 1
                        ]
                    ]
                ]
            ]
        ];

        $doctrine = $this->getMockBuilder(\Doctrine\Bundle\DoctrineBundle\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $diagramOrchestrator = $this->getMockBuilder(DiagramOrchestrator::class)
            ->setMethods(['process'])
            ->setConstructorArgs([$input, $doctrine])
            ->getMock();

        $diagramOrchestrator
            ->expects($this->atLeastOnce())
            ->method('process')
            ->will($this->returnValue(
                [
                    1 => [
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            [
                                'page_view_id'      => 1,
                                'first_byte'       => 450
                            ],
                            [
                                'page_view_id'      => 2,
                                'first_byte'       => 995
                            ],
                            [
                                'page_view_id'      => 4,
                                'first_byte'       => 450
                            ],
                            [
                                'page_view_id'      => 5,
                                'first_byte'       => 450
                            ],
                        ]
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            [
                                'page_view_id'      => 1,
                                'first_byte'       => 450,
                                'page_views_count'  => 1,
                                'first_page_view_Id' => 1,
                                'guid'            => 'guid_1'
                            ],
                            [
                                'page_view_id'      => 2,
                                'first_byte'       => 995,
                                'page_views_count'  => 1,
                                'first_page_view_Id' => 2,
                                'guid'            => 'guid_2'
                            ],
                            [
                                'page_view_id'      => 4,
                                'first_byte'       => 450,
                                'page_views_count'  => 2,
                                'first_page_view_Id' => 3,
                                'guid'            => 'guid_3'
                            ],
                            [
                                'page_view_id'      => 5,
                                'first_byte'       => 450,
                                'page_views_count'  => 1,
                                'first_page_view_Id' => 1,
                                'guid'            => 'guid_4'
                            ],
                        ]
                    ]
                ]
            ));

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input);

        $nonZeroResult = array_filter($result['diagrams'][1]['y']);

        $this->assertEquals(
            [
                2 => '66.67',
                4 => '100.00'
            ],
            $nonZeroResult
        );

    }

    /**
     * @group diagram_builder
     */
    public function testFirstByteCorrectBuckets()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane'
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
                        'filters' => [
                            'device_type' => [
                                'condition'    => 'is',
                                'search_value' => '2'
                            ]
                        ],
                        'technical_metrics' => [
                            'time_to_first_byte' => 1
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
                            'bounce_rate' => 1
                        ]
                    ]
                ]
            ]
        ];

        $doctrine = $this->getMockBuilder(\Doctrine\Bundle\DoctrineBundle\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $diagramOrchestrator = $this->getMockBuilder(DiagramOrchestrator::class)
            ->setMethods(['process'])
            ->setConstructorArgs([$input, $doctrine])
            ->getMock();

        $diagramOrchestrator
            ->expects($this->atLeastOnce())
            ->method('process')
            ->will($this->returnValue(
                [
                    1 => [
                        '2019-07-01 00:00:00' => []
                    ]
                ]
            ));

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input);

        $buckets = $result['diagrams'][0]['x'];

        $this->assertEquals(
            [
                5  => 1000,
                10 => 2000,
                12 => 2400,
                19 => 3800
            ],
            [
                5  => $buckets[5],
                10 => $buckets[10],
                12 => $buckets[12],
                19 => $buckets[19],
            ]
        );
    }


    /**
     * @group diagram_builder
     */
    public function testFirstByteCorrectDiagramName()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane'
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
                        'filters' => [
                            'device_type' => [
                                'condition'    => 'is',
                                'search_value' => '2'
                            ]
                        ],
                        'technical_metrics' => [
                            'time_to_first_byte' => 1
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
                            'bounce_rate' => 1
                        ]
                    ]
                ]
            ]
        ];

        $doctrine = $this->getMockBuilder(\Doctrine\Bundle\DoctrineBundle\Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $diagramOrchestrator = $this->getMockBuilder(DiagramOrchestrator::class)
            ->setMethods(['process'])
            ->setConstructorArgs([$input, $doctrine])
            ->getMock();

        $diagramOrchestrator
            ->expects($this->atLeastOnce())
            ->method('process')
            ->will($this->returnValue(
                [
                    1 => [
                        '2019-07-01 00:00:00' => []
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => []
                    ]
                ]
            ));

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input);

        $this->assertEquals(
            'Bounce Rate',
            $result['diagrams'][1]['name']
        );
    }

}