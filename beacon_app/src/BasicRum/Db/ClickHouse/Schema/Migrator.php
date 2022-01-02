<?php

declare(strict_types=1);

namespace App\BasicRum\Db\ClickHouse\Schema;

use ClickHouseDB\Client;
use \Exception;
use App\BasicRum\Metrics\DbSchemaCollaborator;

class Migrator
{

    /** @var Client */
    private Client $client;

    /** @var DbSchemaCollaborator */
    private DbSchemaCollaborator $dbSchemaCollaborator;

    public function __construct(Client $client, DbSchemaCollaborator $dbSchemaCollaborator)
    {
        $this->client = $client;
        $this->dbSchemaCollaborator = $dbSchemaCollaborator;
    }

    public function updateTableSchema(string $table, array $applicationColumns)
    {
        $res = $this->client->select("DESCRIBE TABLE " . $table);

        $tableColumns = $res->rows();

        $addColumnsMigrator = new Migrator\AddColumns();

        $addColumnStatement = $addColumnsMigrator->getAddColumnsStatementsArr($table, $tableColumns, $applicationColumns);

        foreach ($addColumnStatement as $sql) {
            $this->client->write($sql);
        }
    }

    public function updateAllTablesSchema()
    {
        try {
            $tables = $this->client->showTables();

            foreach ($tables as $table => $data) {
                $this->updateTableSchema(
                    $table,
                    $this->dbSchemaCollaborator->getDbColumnsInfo()
                );
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function createTableIfNotExists(string $table)
    {
        $createTAbleMigrator = new Migrator\CreateTable();

        $this->client->write(
            $createTAbleMigrator->getCreateTableStatement(
                $table,
                $this->dbSchemaCollaborator->getDbColumnsInfo()
            )
        );
    }

}