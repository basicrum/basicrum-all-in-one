<?php

namespace App\Tests\Report\Datak;

use App\BasicRum\Report\Data\BounceRate;
use PHPUnit\Framework\TestCase;

class BounceRateTest extends TestCase
{

    /**
     * @group visit_generate
     */
    public function testCalculateBounceRatePercentageInPeriod()
    {
        $bounceRate = new BounceRate();

        $data = [
            '2019-07-01 00:00:00' => [
                'bounced_buckets' => [
                    0   => 1,
                    200 => 4,
                    600 => 3
                ],
                'all_buckets' => [
                    0   => 1,
                    200 => 7,
                    600 => 3
                ]
            ],
            '2019-07-02 00:00:00' => [
                'bounced_buckets' => [
                    0   => 1,
                    200 => 4,
                    600 => 3
                ],
                'all_buckets' => [
                    0   => 1,
                    200 => 10,
                    600 => 8
                ]
            ]
        ];

        $this->assertEquals(
            [
                0   => '100',
                200 => '47',
                400 => '0',
                600 => '55',
            ],
            $bounceRate->generate($data)
        );
    }

}