<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\Tests\BasicRum\FixturesTestCase;

class DynamicReleaseTest extends FixturesTestCase
{
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        static::bootKernel();
    }

    private function _getDoctrine(): Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * @group diagram_builder
     */
    public function testFourDevicesDistribution()
    {
        $release = self::$kernel->getContainer()->get(Release::class);

        $input = [
            'global' => [
                'presentation' => [
                    'render_type' => 'time_series',
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
                        'name' => 'All traffic',
                        'color' => '#2CA02C',
                        'type' => 'bar',
                    ],
                    'data_requirements' => [
                        'technical_metrics' => [
                            'first_paint' => [
                                'data_flavor' => [
                                    'percentile' => 50,
                                ],
                            ],
                        ],
                    ],
                ],
                2 => [
                    'presentation' => [
                        'name' => 'All traffic',
                        'color' => '#2CA02C',
                        'type' => 'bar',
                    ],
                    'data_requirements' => [
                        'technical_metrics' => [
                            'first_paint' => [
                                'data_flavor' => [
                                    'percentile' => 50,
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
                        '2019-07-30 00:00:00' => ['count' => 33],
                    ],
                    2 => [
                        '2019-07-01 00:00:00' => ['count' => 42],
                        '2019-07-02 00:00:00' => ['count' => 22],
                        '2019-07-03 00:00:00' => ['count' => 55],
                        '2019-07-30 00:00:00' => ['count' => 83],
                    ],
                ]
            );

        $diagramBuilder = new DiagramBuilder();

        $result = $diagramBuilder->build($diagramOrchestrator, $input, $release);

        $mobileResult = array_combine($result['diagrams'][0]['x'], $result['diagrams'][0]['y']);
        $desktopResult = array_combine($result['diagrams'][1]['x'], $result['diagrams'][1]['y']);
        $testArray = [
            'layout' => [
                'shapes' => [
                    [
                        'x0' => $result['layout']['shapes'][0]['x0'],
                        'x1' => $result['layout']['shapes'][0]['x1'],
                    ],
                    [
                        'x0' => $result['layout']['shapes'][1]['x0'],
                        'x1' => $result['layout']['shapes'][1]['x1'],
                    ],
                ],
                'annotations' => [
                    [
                        'x' => $result['layout']['annotations'][0]['x'],
                        'text' => $result['layout']['annotations'][0]['text'],
                    ],
                    [
                        'x' => $result['layout']['annotations'][1]['x'],
                        'text' => $result['layout']['annotations'][1]['text'],
                    ],
                ],
            ],
        ];

        $this->assertEquals(
            [
                'layout' => [
                    'shapes' => [
                        [
                            'x0' => '2019-07-22',
                            'x1' => '2019-07-22',
                        ],
                        [
                            'x0' => '2019-07-14',
                            'x1' => '2019-07-14',
                        ],
                    ],
                    'annotations' => [
                        [
                            'x' => '2019-07-22',
                            'text' => 'Test release #2',
                        ],
                        [
                            'x' => '2019-07-14',
                            'text' => 'Test release #1',
                        ],
                    ],
                ],
            ],
            $testArray
        );
    }
}
