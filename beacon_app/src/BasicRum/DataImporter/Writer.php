<?php

namespace App\BasicRum\DataImporter;

use ClickHouseDB\Client;

class Writer
{
    /** @var Client */
    private Client $client;

    public function __construct()
    {
        // Test Connection to ClickHouse
        $config = [
            'host' => getenv('CLICKHOUSE_HOST'),
            'port' => getenv('CLICKHOUSE_PORT'),
            'username' => getenv('CLICKHOUSE_USER'),
            'password' => getenv('CLICKHOUSE_PASS')
        ];

        $this->client = new Client($config);
        var_dump($this->client->ping());
    }

    public function runImport($host, $data, $batchSize): int
    {
        $this->createTableIfExists($host);

        $chunks = array_chunk($data, $batchSize);

        foreach ($chunks as $chunk) {
            $stat = $this->client->insert(
                $this->getTableName($host),
                $chunk,
                array_keys($chunk[0])
            );
        }

        return count($data);
    }

    private function createTableIfExists($host)
    {
        $this->client->write('
            CREATE TABLE IF NOT EXISTS ' . $this->getTableName($host) . ' (
                event_date Date DEFAULT toDate(simple_bm_created_at),
                simple_bm_created_at DateTime,
                simple_tm_connect_duration Nullable(UInt16),
                simple_tm_first_contentful_paint UInt16,
                simple_tm_first_paint UInt16,
                simple_tm_load_event_end Nullable(UInt16),
                simple_tm_redirects_count UInt8,
                simple_tm_time_to_first_byte Nullable(UInt16),
                simple_tm_dns_duration Nullable(UInt16),
                simple_tm_redirect_duration Nullable(UInt16),
                simple_tm_download_time Nullable(UInt16),
                simple_bm_session_id FixedString(43),
                simple_bm_user_agent String,
                simple_bm_request_type LowCardinality(String),
                derived_bm_browser_name LowCardinality(String),
                derived_bm_browser_version String,
                derived_bm_device_type LowCardinality(String),
                derived_bm_device_manufacturer LowCardinality(String)
            )
                ENGINE = MergeTree()
                PARTITION BY toYYYYMMDD(event_date)
                ORDER BY (derived_bm_device_type, event_date)
                SETTINGS index_granularity = 8192
        ');
    }

    private function getTableName($host)
    {
        return 'rum_' . $host .'_data_flat';
    }

}
