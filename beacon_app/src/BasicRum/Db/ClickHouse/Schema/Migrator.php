<?php

declare(strict_types=1);

namespace App\BasicRum\Db\ClickHouse\Schema;

use App\BasicRum\Db\ClickHouse\Connection;
use \Exception;
use App\BasicRum\Metrics\DbSchemaCollaborator;

class Migrator
{

    /** @var Connection */
    private Connection $connection;

    /** @var DbSchemaCollaborator */
    private DbSchemaCollaborator $dbSchemaCollaborator;

    public function __construct(Connection $connection, DbSchemaCollaborator $dbSchemaCollaborator)
    {
        $this->connection = $connection;
        $this->dbSchemaCollaborator = $dbSchemaCollaborator;
    }

    public function updateTableSchema(string $table, array $applicationColumns)
    {
        $tableColumns = $this->connection->selectRows("DESCRIBE TABLE " . $table);

        $addColumnsMigrator = new Migrator\AddColumns();

        $addColumnStatement = $addColumnsMigrator->getAddColumnsStatementsArr($table, $tableColumns, $applicationColumns);

        foreach ($addColumnStatement as $sql) {
            $this->connection->write($sql);
        }
    }

    public function updateAllTablesSchema()
    {
        try {
            $tables = $this->connection->showTables();

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

        $this->connection->write(
            $createTAbleMigrator->getCreateTableStatement(
                $table,
                $this->dbSchemaCollaborator->getDbColumnsInfo()
            )
        );
    }

}
