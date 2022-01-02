<?php

namespace App\BasicRum\DataImporter;

use ClickHouseDB\Client;
use App\BasicRum\Db\ClickHouse\Schema\Migrator;
use App\BasicRum\Metrics\DbSchemaCollaborator;

class Writer
{
    /** @var Client */
    private Client $client;

    /** @var Migrator */
    private Migrator $migrator;

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

        $this->migrator = new Migrator($this->client, new DbSchemaCollaborator());

        var_dump($this->client->ping());
    }

    public function runImport($host, $data, $batchSize): int
    {
        $table = $this->getTableName($host);

        $this->migrator->createTableIfNotExists($table);

        $chunks = array_chunk($data, $batchSize);

        foreach ($chunks as $chunk) {
            $stat = $this->client->insert(
                $table,
                $chunk,
                array_keys($chunk[0])
            );
        }

        return count($data);
    }

    private function getTableName($host)
    {
        return 'rum_' . $host .'_data_flat';
    }

}
