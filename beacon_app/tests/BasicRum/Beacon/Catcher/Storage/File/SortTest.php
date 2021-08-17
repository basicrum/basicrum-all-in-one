<?php

namespace  App\Tests\BasicRum\Beacon\Catcher\Storage\File;

use PHPUnit\Framework\TestCase;


class SortTest extends TestCase
{

    /**
     * @group catcher
     */
    public function testBeaconsSortedFromLowToHigh()
    {
        $data = [
            [
                0 => 1554611874,
                1 => 'dummy'
            ],
            [
                0 => 1554612260,
                1 => 'dummy'
            ],
            [
                0 => 1554611952,
                1 => 'dummy'
            ]
        ];

        $expectations = [
            [
                0 => 1554611874,
                1 => 'dummy'
            ],
            [
                0 => 1554611952,
                1 => 'dummy'
            ],
            [
                0 => 1554612260,
                1 => 'dummy'
            ]
        ];

        $sort = new \App\BasicRum\Beacon\Catcher\Storage\File\Sort();

        $sort->sortBeacons($data);

        $this->assertEquals(
            $expectations,
            $data
        );
    }

}