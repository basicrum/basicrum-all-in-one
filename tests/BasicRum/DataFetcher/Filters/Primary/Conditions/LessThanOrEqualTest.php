<?php

namespace App\Tests\BasicRum\DataFetcher\Filters\Primary\Conditions;

use  App\Tests\BasicRum\DataFetcher\DataFetcherFixtureTestCase;
use App\BasicRum\Periods\Period;

class LessThanOrEqualTest extends DataFetcherFixtureTestCase
{
    /**
     * @group data_fetcher
     */
    public function testFirstByteDeviceTypeFoundRecords()
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
                        "field"    => "device_type_id",
                        "operator" => "less_than_or_equal",
                        "value"    => "2",
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
                            'first_byte' => 150,
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
    public function testFirstByteDeviceTypeNoRecords()
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
                        "field"    => "device_type_id",
                        "operator" => "less_than_or_equal",
                        "value"    => "0",
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
