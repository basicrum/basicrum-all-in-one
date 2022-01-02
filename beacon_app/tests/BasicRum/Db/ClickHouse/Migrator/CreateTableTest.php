<?php

declare(strict_types=1);

namespace App\Tests\BasicRum\Db\ClickHouse\Migrator;

use App\BasicRum\Db\ClickHouse\Schema\Migrator\CreateTable;

use App\BasicRum\Metrics\DbSchemaCollaborator;

use PHPUnit\Framework\TestCase;

class CreateTableTest extends TestCase
{

    /**
     * @group schema
     */
    public function testCreateTableSql()
    {
        $createTable = new CreateTable();

        $collaborator = new DbSchemaCollaborator();

        $this->assertEquals(<<<END
CREATE TABLE IF NOT EXISTS rum_data_flat (
    event_date Date DEFAULT toDate(created_at),
    connect_duration Nullable(UInt16),
    first_contentful_paint Nullable(UInt16),
    cumulative_layout_shift Nullable(Float32),
    largest_contentful_paint Nullable(UInt16),
    first_input_delay Nullable(UInt16),
    first_paint Nullable(UInt16),
    load_event_end Nullable(UInt16),
    redirects_count UInt8,
    first_byte Nullable(UInt16),
    dns_duration Nullable(UInt16),
    redirect_duration Nullable(UInt16),
    download_time Nullable(UInt16),
    session_id FixedString(43),
    session_length UInt8,
    url String,
    user_agent String,
    request_type LowCardinality(String),
    created_at DateTime,
    browser_name LowCardinality(String),
    browser_version String,
    device_type LowCardinality(String),
    device_manufacturer LowCardinality(String)
)
    ENGINE = MergeTree()
    PARTITION BY toYYYYMMDD(event_date)
    ORDER BY (device_type, event_date)
    SETTINGS index_granularity = 8192
END
        ,
        $createTable->getCreateTableStatement("rum_data_flat", $collaborator->getDbColumnsInfo()));
    }


}