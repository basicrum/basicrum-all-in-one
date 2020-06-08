<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\Tests\BasicRum\FixturesTestCase;

class SegmentsDeviceDistributionTest extends FixturesTestCase
{
    private $release;

    public function setUp()
    {
        parent::setUp();
        $this->release = self::$kernel->getContainer()->get(Release::class);
    }

    /**
     * @group diagram_builder
     */
    public function testFourDevicesDistribution()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'distribution',
                ],
                'data_requirements' => [
                    'period' => [
                        'type' => 'moving',
                        'start' => '20',
                        'end' => 'now',
                    ],
                ],
            ],
            'segments' => [
                1 => [
                    'presentation' => [
                        'name' => 'Desktop',
                        'color' => '#1F77B4',
                    ],
                    'data_requirements' => [
                        'filters' => [
                            'device_type' => [
                                'search_value' => '2',
                                'condition' => 'is',
                            ],
                        ],
                        'business_metrics' => [
                            'page_views_count' => [
                                'data_flavor' => [
                                    'count' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                2 => [
                    'presentation' => [
                        'name' => 'Tablet',
                        'color' => '#ff6023',
                    ],
                    'data_requirements' => [
                        'filters' => [
                            'device_type' => [
                                'search_value' => '3',
                                'condition' => 'is',
                            ],
                        ],
                        'business_metrics' => [
                            'page_views_count' => [
                                'data_flavor' => [
                                    'count' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                3 => [
                    'presentation' => [
                        'name' => 'Mobile',
                        'color' => '#2CA02C',
                    ],
                    'data_requirements' => [
                        'filters' => [
                            'device_type' => [
                                'search_value' => '1',
                                'condition' => 'is',
                            ],
                        ],
                        'business_metrics' => [
                            'page_views_count' => [
                                'data_flavor' => [
                                    'count' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                4 => [
                    'presentation' => [
                        'name' => 'Bot',
                        'color' => '#000000',
                    ],
                    'data_requirements' => [
                        'filters' => [
                            'device_type' => [
                                'search_value' => '4',
                                'condition' => 'is',
                            ],
                        ],
                        'business_metrics' => [
                            'page_views_count' => [
                                'data_flavor' => [
                                    'count' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $diagramOrchestrator = $this->getMockBuilder(DiagramOrchestrator::class)
            ->setMethods(['process'])
            ->disableOriginalConstructor()
            ->getMock();

        $diagramOrchestrator
            ->expects($this->atLeastOnce())
            ->method('process')
            ->willReturn(
                [
                    1 => [
                        '2019-07-01 00:00:00' => ['count' => 42],
                        '2019-07-02 00:00:00' => ['count' => 32],
                        '2019-07-03 00:00:00' => ['count' => 45],
                        '2019-07-04 00:00:00' => ['count' => 33],
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => ['count' => 42],
                        '2019-07-02 00:00:00' => ['count' => 22],
                        '2019-07-03 00:00:00' => ['count' => 55],
                        '2019-07-04 00:00:00' => ['count' => 83],
                    ],
                    3 => [
                        '2019-07-01 00:00:00' => ['count' => 2],
                        '2019-07-02 00:00:00' => ['count' => 38],
                        '2019-07-03 00:00:00' => ['count' => 43],
                        '2019-07-04 00:00:00' => ['count' => 12],
                    ],
                    4 => [
                        '2019-07-01 00:00:00' => ['count' => 43],
                        '2019-07-02 00:00:00' => ['count' => 42],
                        '2019-07-03 00:00:00' => ['count' => 15],
                        '2019-07-04 00:00:00' => ['count' => 43],
                    ],
                ]
            );

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input, $this->release);

        $mobileResult = array_combine($result['diagrams'][0]['x'], $result['diagrams'][0]['y']);
        $desktopResult = array_combine($result['diagrams'][1]['x'], $result['diagrams'][1]['y']);
        $tabletResult = array_combine($result['diagrams'][2]['x'], $result['diagrams'][2]['y']);
        $botResult = array_combine($result['diagrams'][3]['x'], $result['diagrams'][3]['y']);

        $this->assertEquals(
            [
                [
                    '2019-07-01 00:00:00' => 32.56,
                    '2019-07-02 00:00:00' => 23.88,
                    '2019-07-03 00:00:00' => 28.48,
                    '2019-07-04 00:00:00' => 19.30,
                ],
                [
                    '2019-07-01 00:00:00' => 32.56,
                    '2019-07-02 00:00:00' => 16.42,
                    '2019-07-03 00:00:00' => 34.81,
                    '2019-07-04 00:00:00' => 48.54,
                ],
                [
                    '2019-07-01 00:00:00' => 1.55,
                    '2019-07-02 00:00:00' => 28.36,
                    '2019-07-03 00:00:00' => 27.22,
                    '2019-07-04 00:00:00' => 7.02,
                ],
                [
                    '2019-07-01 00:00:00' => 33.33,
                    '2019-07-02 00:00:00' => 31.34,
                    '2019-07-03 00:00:00' => 9.49,
                    '2019-07-04 00:00:00' => 25.15,
                ],
            ],
            [
                $mobileResult,
                $desktopResult,
                $tabletResult,
                $botResult,
            ]
        );
    }
}
