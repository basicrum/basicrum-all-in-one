<?php

namespace App\Tests\BasicRum\DataFetcher\DataFlavors\Count;

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
        $period->setPeriod('10/28/2018', '10/28/2018');

        $res = $this->getDataLayer()->fetch(
            // Fields
            [
                "first_byte" => [
                    "source" => "rum_data_flat",
                    "field"  => "rum_data_id"
                ]
            ],
            // Data Flavor
            [
                "type" => "count",
                "params" => []
            ],
            // Filters
            [],
            // Period
            $period,
        );

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' => [
                    'count' => 4
                ]
            ],
            $res
        );
    }
}
