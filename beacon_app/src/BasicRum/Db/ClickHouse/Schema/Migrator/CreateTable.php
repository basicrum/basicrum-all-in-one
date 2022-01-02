<?php

declare(strict_types=1);

namespace App\BasicRum\Db\ClickHouse\Schema\Migrator;

class CreateTable
{

    public function getCreateTableStatement(string $table, array $applicationColumns): string
    {
        $columnsParts = [];
        
        foreach ($applicationColumns as $column) {
            $columnsParts[] = $column["name"] . " " . $column["type"];
        }

        $columnsPartsSql = implode(",\n    ", $columnsParts);

        $createTableSql = <<<END
CREATE TABLE IF NOT EXISTS $table (
    event_date Date DEFAULT toDate(created_at),
    $columnsPartsSql
)
    ENGINE = MergeTree()
    PARTITION BY toYYYYMMDD(event_date)
    ORDER BY (device_type, event_date)
    SETTINGS index_granularity = 8192
END;

        return $createTableSql;
    }

}