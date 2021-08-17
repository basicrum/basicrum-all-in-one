<?php

namespace App\Tests\BasicRum\DataFetcher\DataFlavors\LandingPageHistogram;

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
                "type" => "landing_page_histogram",
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
                    'all_buckets' => [
                        '300' => 1
                    ]
                ],
            ],
            $res
        );
    }
}
