<?php

namespace App\Tests\Report\Datak;

use App\BasicRum\Report\Data\Histogram;
use PHPUnit\Framework\TestCase;

class HistogramTest extends TestCase
{

    /**
     * @group visit_generate
     */
    public function testGenerateHistogramInPeriod()
    {
        $histogram = new Histogram();

        $data = [
            '2019-07-01 00:00:00' => [
                'all_buckets' => [
                    0   => 1,
                    200 => 7,
                    600 => 3
                ]
            ],
            '2019-07-02 00:00:00' => [
                'all_buckets' => [
                    0   => 1,
                    200 => 10,
                    600 => 8
                ]
            ],
            '2019-07-03 00:00:00' => [
                'all_buckets' => [
                    0    => 1,
                    200  => 90,
                    600  => 8,
                    1000 => 10
                ]
            ]
        ];

        $this->assertEquals(
            [
                0    => '3',
                200  => '107',
                400  => '0',
                600  => '19',
                800  => '0',
                1000 => '10',
            ],
            $histogram->generate($data)
        );
    }

}