<?php

declare(strict_types=1);

namespace App\Tests\BasicRum\Db\ClickHouse\Migrator;

use App\BasicRum\Db\ClickHouse\Schema\Migrator\AddColumns;

use PHPUnit\Framework\TestCase;

class AddColumnsTest extends TestCase
{

    /**
     * @group schema
     */
    public function testAddColumnsStatement()
    {
        $alterTable = new AddColumns();

        $applicationColumns = [
            "col_1" => [
                'name' => 'col_1',
                'type' => 'Nullable(UInt16)'
            ],
            "col_2" => [
                'name' => 'col_2',
                'type' => 'Nullable(UInt8)'
            ],
            "col_3" => [
                'name' => 'col_3',
                'type' => 'Nullable(Float32)'
            ],
            "col_4" => [
                'name' => 'col_4',
                'type' => 'LowCardinality(String)'
            ]
        ];

        $tableColumns = [
            [
                "name" => "col_1",
                "type" => "Nullable(UInt16)",
                "default_type" => "",
                "default_expression" => "",
                "comment" => "",
                "codec_expression" => "",
                "ttl_expression" => ""
            ]
        ];

        $this->assertEquals([
            "ALTER TABLE rum_data_flat ADD COLUMN col_2 Nullable(UInt8)",
            "ALTER TABLE rum_data_flat ADD COLUMN col_3 Nullable(Float32)",
            "ALTER TABLE rum_data_flat ADD COLUMN col_4 LowCardinality(String)"
        ],
        $alterTable->getAddColumnsStatementsArr("rum_data_flat", $tableColumns, $applicationColumns));
    }

}