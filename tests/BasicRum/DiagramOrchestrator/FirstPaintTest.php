<?php

namespace App\Tests\BasicRum\Layers\DataLayer\Query;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use App\BasicRum\DiagramOrchestrator;

class FirstPaintTest extends KernelTestCase
{

    protected function setUp()
    {
        static::bootKernel();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    private function _getDoctrine() : \Doctrine\Bundle\DoctrineBundle\Registry
    {
        return static::$kernel->getContainer()->get('doctrine');
    }

    public function testFirstPaintSelected()
    {
        $diagramOrchestrator = new DiagramOrchestrator($this->_getDoctrine());

        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => 'mobile'
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
            ]
        ];

        $diagramOrchestrator->fillRequirements($requirementsArr);

        $res = $diagramOrchestrator->process();

        $this->assertEquals(
            [
                [
                    [
                        [
                            'pageViewId' => 1,
                            'firstPaint' => 344
                        ]
                    ]
                ]
            ],
            $res
        );
    }


    public function testFirstPaintNotSelected()
    {
        $diagramOrchestrator = new DiagramOrchestrator($this->_getDoctrine());

        $requirementsArr = [
            'filters' => [
                'device_type' => [
                    'condition'    => 'is',
                    'search_value' => 'mobile'
                ]
            ],
            'periods' => [
                [
                    'from_date' => '10/24/2018',
                    'to_date'   => '10/24/2018'
                ]
            ]
        ];

        $diagramOrchestrator->fillRequirements($requirementsArr);

        $res = $diagramOrchestrator->process();

        $this->assertEquals(
            [
                [
                    [
                        [
                            'pageViewId' => 1
                        ]
                    ]
                ]
            ],
            $res
        );
    }

}