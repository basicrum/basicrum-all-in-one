<?php

namespace App\Tests\BasicRum\Visit\Calculator\Aggregator;

use App\BasicRum\Visit\Calculator\Aggregator\Chunk;
use App\Tests\BasicRum\FixturesTestCase;

class ChunkTest extends FixturesTestCase
{
    protected function setUp()
    {
        static::bootKernel();
    }

    public function testChunkenizeTwoSeparateVisits()
    {
        $pageViews = [
            [
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
            ],
            [
                'createdAt' => new \DateTime('2018-10-28 13:32:33'),
                'rumDataId' => 3,
            ],
        ];

        $chunk = new Chunk();

        $res = $chunk->chunkenize($pageViews, 30);

        $this->assertEquals(
            [
                [
                    'begin' => 2,
                    'end' => 2,
                ],
                [
                    'begin' => 3,
                    'end' => 3,
                ],
            ],
            $res
        );
    }

    public function testChunkenizeCompleteVisitWithMoreThanOneViewAndAnotherNotCompletedVisitWithOneView()
    {
        $pageViews = [
            [
                'createdAt' => new \DateTime('2018-10-25 13:30:33'),
                'rumDataId' => 1,
            ],
            [
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
            ],
            [
                'createdAt' => new \DateTime('2018-10-28 13:32:33'),
                'rumDataId' => 3,
            ],
        ];

        $chunk = new Chunk();

        $res = $chunk->chunkenize($pageViews, 30);

        $this->assertEquals(
            [
                [
                    'begin' => 1,
                    'end' => 2,
                ],
                [
                    'begin' => 3,
                    'end' => 3,
                ],
            ],
            $res
        );
    }

    public function testChunkenizeCompleteVisitWithMoreThanOneViewAndAnotherNotCompletedVisitWithMoreThanOneView()
    {
        $pageViews = [
            [
                'createdAt' => new \DateTime('2018-10-25 13:30:33'),
                'rumDataId' => 1,
            ],
            [
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
            ],
            [
                'createdAt' => new \DateTime('2018-10-28 13:32:33'),
                'rumDataId' => 3,
            ],
            [
                'createdAt' => new \DateTime('2018-10-28 13:38:22'),
                'rumDataId' => 4,
            ],
        ];

        $chunk = new Chunk();

        $res = $chunk->chunkenize($pageViews, 30);

        $this->assertEquals(
            [
                [
                    'begin' => 1,
                    'end' => 2,
                ],
                [
                    'begin' => 3,
                    'end' => 4,
                ],
            ],
            $res
        );
    }

    public function testChunkenizeOneVisit()
    {
        $pageViews = [
            [
                'createdAt' => new \DateTime('2018-10-25 13:32:33'),
                'rumDataId' => 2,
            ],
        ];

        $chunk = new Chunk();

        $res = $chunk->chunkenize($pageViews, 30);

        $this->assertEquals(
            [
                [
                    'begin' => 2,
                    'end' => 2,
                ],
            ],
            $res
        );
    }
}
