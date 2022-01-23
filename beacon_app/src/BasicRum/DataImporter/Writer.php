<?php

namespace App\BasicRum\DataImporter;

use App\BasicRum\Db\ClickHouse\Connection;
use App\BasicRum\Db\ClickHouse\Schema\Migrator;
use App\BasicRum\Metrics\DbSchemaCollaborator;

class Writer
{
    /** @var Connection */
    private Connection $connection;

    /** @var Migrator */
    private Migrator $migrator;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;

        $this->migrator = new Migrator($connection, new DbSchemaCollaborator());
    }

    public function runImport(string $host, array $data, int $batchSize): int
    {
        $table = $this->getTableName($host);

        $this->migrator->createTableIfNotExists($table);

        $chunks = array_chunk($data, $batchSize);

        foreach ($chunks as $chunk) {
            $this->connection->insert(
                $table,
                $chunk,
                array_keys($chunk[0])
            );
        }

        return count($data);
    }

    private function getTableName($host) : string
    {
        return 'rum_' . $host .'_data_flat';
    }

}
