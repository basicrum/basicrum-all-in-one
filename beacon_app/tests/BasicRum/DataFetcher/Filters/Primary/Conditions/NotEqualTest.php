<?php

namespace App\Tests\BasicRum\DataFetcher\Filters\Primary\Conditions;

use  App\Tests\BasicRum\DataFetcher\DataFetcherFixtureTestCase;
use App\BasicRum\Periods\Period;

class NotEqualTest extends DataFetcherFixtureTestCase
{
    /**
     * @group data_fetcher
     */
    public function testFirstByteDeviceTypeFoundAllRecords()
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
                        "operator" => "not_equal",
                        "value"    => "99",
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
    public function testFirstByteDeviceTypeExistingRecordIgnored()
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
                        "operator" => "not_equal",
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
                    ],
                ],
            ],
            $res
        );
    }
}
