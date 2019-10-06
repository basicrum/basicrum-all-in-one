<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use PHPUnit\Framework\TestCase;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;

class FirstByteTest extends TestCase
{

    /**
     * @group diagram_builder
     */
    public function testFirstByteValuesInBuckets()
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
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            [
                                'pageViewId' => 1,
                                'firstByte'  => 420,
                            ],
                            [
                                'pageViewId' => 2,
                                'firstByte'  => 995,
                            ],
                            [
                                'pageViewId' => 5,
                                'firstByte'  => 450,
                            ],
                            [
                                'pageViewId' => 8,
                                'firstByte'  => 1999,
                            ],
                            [
                                'pageViewId' => 11,
                                'firstByte'  => 1022,
                            ],
                        ]
                    ]
                ]
            ));

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input);

        $nonZeroResult = array_filter($result['diagrams'][0]['y']);

        $this->assertEquals(
            [
                2 => 2,
                4 => 1,
                5 => 1,
                9 => 1
            ],
            $nonZeroResult
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
                        'name' => 'First Byte',
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
                    ],
                ]
            ));

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input);

        $this->assertEquals(
            'First Byte',
            $result['diagrams'][0]['name']
        );
    }

}