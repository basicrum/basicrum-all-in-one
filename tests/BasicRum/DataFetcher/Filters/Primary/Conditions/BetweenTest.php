<?php

namespace App\Tests\BasicRum\DataFetcher\Filters\Primary\Conditions;

use  App\Tests\BasicRum\DataFetcher\DataFetcherFixtureTestCase;
use App\BasicRum\Periods\Period;

class BetweenTest extends DataFetcherFixtureTestCase
{
    /**
     * @group data_fetcher
     */
    public function testFirstByteFoundRecords()
    {
        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

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
                "type" => "data_rows",
                "params" => []
            ],
            // Filters
            [
                "condition" => "AND",
                "rules" => [
                    [
                        "source"   => "rum_data_flat",
                        "field"    => "first_byte",
                        "operator" => "between",
                        "value"    => "2000,2500",
                        "type"     => "primary"
                    ]
                ]
            ],
            // Period
            $period,
        );

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' => [
                    'data_rows' => [
                        [
                            'first_byte' => 2150,
                        ],
                        [
                            'first_byte' => 2150,
                        ],
                        [
                            'first_byte' => 2150,
                        ],
                    ],
                ],
            ],
            $res
        );
    }

    /**
     * @group data_fetcher
     */
    public function testFirstByteNoRecords()
    {
        $period = new Period();
        $period->setPeriod('10/28/2018', '10/28/2018');

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
                "type" => "data_rows",
                "params" => []
            ],
            // Filters
            [
                "condition" => "AND",
                "rules" => [
                    [
                        "source"   => "rum_data_flat",
                        "field"    => "first_byte",
                        "operator" => "between",
                        "value"    => "3000,4000",
                        "type"     => "primary"
                    ]
                ]
            ],
            // Period
            $period,
        );

        $this->assertEquals(
            [
                '2018-10-28 00:00:00' => [
                    'data_rows' => [],
                ],
            ],
            $res
        );
    }
}
