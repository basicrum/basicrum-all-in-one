<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\Tests\BasicRum\FixturesTestCase;

class FirstByteAndBounceRateTest extends FixturesTestCase
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
    public function testBounceRateValuesInBuckets()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane',
                ],
            ],
            'segments' => [
                1 => [
                    'presentation' => [
                        'name' => 'Sessions',
                        'color' => '#ff0000',
                    ],
                    'data_requirements' => [
                        'technical_metrics' => [
                            'first_paint' => [
                                'data_flavor' => [
                                    'histogram' => [
                                        'bucket' => '200',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                2 => [
                    'presentation' => [
                        'name' => 'Bounce Rate',
                        'color' => '#000000',
                    ],
                    'data_requirements' => [
                        'business_metrics' => [
                            'bounce_rate' => [
                                'data_flavor' => [
                                    'bounce_rate' => [
                                        'in_metric' => 'first_paint',
                                    ],
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
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            'all_buckets' => [
                                200 => 5,
                                400 => 2,
                            ],
                        ],
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            'bounced_buckets' => [
                                400 => 3,
                                800 => 2,
                            ],
                            'all_buckets' => [
                                400 => 5,
                                800 => 2,
                            ],
                        ],
                    ],
                ]
            );

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input, $this->release);

        $nonZeroResult = array_filter($result['diagrams'][1]['y']);

        $this->assertEquals(
            [
                2 => '60',
                4 => '100',
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
                    'render_type' => 'plane',
                ],
            ],
            'segments' => [
                1 => [
                    'presentation' => [
                        'name' => 'Sessions',
                        'color' => '#ff0000',
                    ],
                    'data_requirements' => [
                        'technical_metrics' => [
                            'first_byte' => [
                                'data_flavor' => [
                                    'histogram' => [
                                        'bucket' => '200',
                                    ],
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
                        '2019-07-01 00:00:00' => [
                            'all_buckets' => [
                                0 => 0,
                                200 => 0,
                                400 => 0,
                                600 => 0,
                                800 => 0,
                            ],
                        ],
                    ],
                ]
            );

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input, $this->release);

        //var_dump($result);

        $buckets = $result['diagrams'][0]['x'];

        $this->assertEquals(
            [
                1 => 200,
                2 => 400,
                4 => 800,
            ],
            [
                1 => $buckets[1],
                2 => $buckets[2],
                4 => $buckets[4],
            ]
        );
    }

    /**
     * @group diagram_builder
     */
    public function testBounceRateAndFirstByteCorrectDiagramNames()
    {
        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'plane',
                ],
            ],
            'segments' => [
                1 => [
                    'presentation' => [
                        'name' => 'Sessions',
                        'color' => '#ff0000',
                    ],
                    'data_requirements' => [
                        'technical_metrics' => [
                            'first_paint' => [
                                'data_flavor' => [
                                    'histogram' => [
                                        'bucket' => '200',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                2 => [
                    'presentation' => [
                        'name' => 'Bounce Rate',
                        'color' => '#000000',
                    ],
                    'data_requirements' => [
                        'business_metrics' => [
                            'bounce_rate' => [
                                'data_flavor' => [
                                    'bounce_rate' => [
                                        'in_metric' => 'first_paint',
                                    ],
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
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            'all_buckets' => [
                                200 => 5,
                                400 => 2,
                            ],
                        ],
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => [],
                        '2019-07-02 00:00:00' => [
                            'bounced_buckets' => [
                                400 => 3,
                                800 => 2,
                            ],
                            'all_buckets' => [
                                400 => 5,
                                800 => 2,
                            ],
                        ],
                    ],
                ]
            );

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input, $this->release);

        $this->assertEquals(
            'Bounce Rate',
            $result['diagrams'][1]['name']
        );

        $this->assertEquals(
            'Sessions',
            $result['diagrams'][0]['name']
        );
    }
}
