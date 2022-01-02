<?php

declare(strict_types=1);

namespace App\Tests\BasicRum\Db\ClickHouse;

use App\BasicRum\Db\ClickHouse\Schema\Migrator;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;
use PHPUnit\Framework\TestCase;
use App\BasicRum\Metrics\DbSchemaCollaborator;

class MigratorTest extends TestCase
{

    /**
     * @group schema
     */
    public function testAddColumnsToExistingTables()
    {
        $clickHouseClientMock = $this->getMockBuilder(Client::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $clickHouseClientMock->method('showTables')
            ->willReturn([
                "rum_data_flat" => [
                    "name" => "rum_data_flat"
                ],
                "rum_data_flat_2" => [
                    "name" => "rum_data_flat_2"
                ]
            ]);
        
        $clickHouseClientMock->expects($this->exactly(2))
            ->method('select')
            ->withConsecutive(
                ['DESCRIBE TABLE rum_data_flat'],
                ['DESCRIBE TABLE rum_data_flat_2']
            )
            ->will($this->returnCallback(array($this, 'returnSelectStatementCallback')));

        $clickHouseClientMock->expects($this->exactly(5))
            ->method('write')
            ->withConsecutive(
                ['ALTER TABLE rum_data_flat ADD COLUMN col_2 Nullable(UInt8)'],
                ['ALTER TABLE rum_data_flat ADD COLUMN col_3 Nullable(Float32)'],
                ['ALTER TABLE rum_data_flat ADD COLUMN col_4 LowCardinality(String)'],
                ['ALTER TABLE rum_data_flat_2 ADD COLUMN col_3 Nullable(Float32)'],
                ['ALTER TABLE rum_data_flat_2 ADD COLUMN col_4 LowCardinality(String)']
            );

        $dbSchemaCollaboratorMock = $this->getMockBuilder(DbSchemaCollaborator::class)
            ->getMock();

        $dbSchemaCollaboratorMock->method('getDbColumnsInfo')
            ->willReturn([
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
            ]);

        $migrator = new Migrator($clickHouseClientMock, $dbSchemaCollaboratorMock);

        $migrator->updateAllTablesSchema();
    }

    public function returnSelectStatementCallback(): Statement
    {
        $args = func_get_args();

        $selectQuery = $args[0];
        
        $statementMock = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->getMock();

        if ("DESCRIBE TABLE rum_data_flat" === $selectQuery) {
            $columns = [
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
        }

        if ("DESCRIBE TABLE rum_data_flat_2" === $selectQuery) {
            $columns = [
                [
                    "name" => "col_1",
                    "type" => "Nullable(UInt16)",
                    "default_type" => "",
                    "default_expression" => "",
                    "comment" => "",
                    "codec_expression" => "",
                    "ttl_expression" => ""
                ],
                [
                    "name" => "col_2",
                    "type" => "Nullable(UInt8)",
                    "default_type" => "",
                    "default_expression" => "",
                    "comment" => "",
                    "codec_expression" => "",
                    "ttl_expression" => ""
                ]
            ];
        }

        $statementMock->expects($this->once())
            ->method('rows')
            ->willReturn($columns);

        return $statementMock;
    }

}