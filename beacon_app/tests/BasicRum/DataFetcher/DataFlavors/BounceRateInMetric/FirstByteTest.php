<?php

namespace App\Tests\BasicRum\DataFetcher\DataFlavors\BounceRateInMetric;

use  App\Tests\BasicRum\DataFetcher\DataFetcherFixtureTestCase;
use App\BasicRum\Periods\Period;

class FirstByteTest extends DataFetcherFixtureTestCase
{
    /**
     * @group data_fetcher
     */
    public function testFirstByteNoFilter()
    {
        $period = new Period();
        $period->setPeriod('10/24/2018', '10/24/2018');

        $res = $this->getDataLayer()->fetch(
            // Fields
            [
                "first_byte" => [
                    "source" => "rum_data_flat",
                    "field"  => "first_byte"
                ]
            ],
            // Data Flavor
            [
                "type" => "bounce_rate_in_metric",
                "params" => [
                    "bucket" => 50
                ]
            ],
            // Filters
            [],
            // Period
            $period,
        );

        $this->assertEquals(
            [
                '2018-10-24 00:00:00' => [
                    'bounced_buckets' => [
                        '300' => 1
                    ],
                    'all_buckets' => [
                        '300' => 1
                    ]
                ],
            ],
            $res
        );
    }
}
