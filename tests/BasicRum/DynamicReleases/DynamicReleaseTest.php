<?php

namespace App\Tests\BasicRum\DiagramBuilder;

use App\BasicRum\DiagramBuilder;
use App\BasicRum\DiagramOrchestrator;
use App\BasicRum\Release;
use App\Entity\Releases;
use App\Tests\BasicRum\FixturesTestCase;

class DynamicReleaseTest extends FixturesTestCase
{
    private function _getDoctrine(): \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    /**
     * Check if releases table was populated with test data.
     */
    public function testIfDBContainsReleases()
    {
        $em = $this->_getDoctrine()->getManager();
        $releases = $em->getRepository(Releases::class)->findAll();

        $referenceArray = [
            [
                'id' => 1,
                'date' => '2019-07-14 00:00:00',
                'description' => 'Test release #1',
            ],
            [
                'id' => 2,
                'date' => '2019-07-22 00:00:00',
                'description' => 'Test release #2',
            ],
        ];

        $testArray = [
            [
                'id' => $releases[0]->getId(),
                'date' => $releases[0]->getDate()->format('Y-m-d H:i:s'),
                'description' => $releases[0]->getDescription(),
            ],
            [
                'id' => $releases[1]->getId(),
                'date' => $releases[1]->getDate()->format('Y-m-d H:i:s'),
                'description' => $releases[1]->getDescription(),
            ],
        ];

        $this->assertEquals($referenceArray, $testArray);
    }

    /**
     * @group diagram_builder
     */
    public function testDiagramContainReleasesInformation()
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
