<?php

declare(strict_types=1);

namespace App\BasicRum\Db\ClickHouse\Schema\Migrator;

class AddColumns
{

    public function getAddColumnsStatementsArr(string $table, array $tableColumns, array $applicationColumns): array
    {
        $addColumnsArray = [];

        $newColumns = $applicationColumns;

        foreach ($tableColumns as $tableColumn) {
            if (isset($applicationColumns[$tableColumn["name"]])) {
                unset($newColumns[$tableColumn["name"]]);
            }
        }

        foreach ($newColumns as $newColumn) {
            $addColumnsArray[] = "ALTER TABLE " . $table . " ADD COLUMN " . $newColumn["name"] . " " . $newColumn["type"];
        }

        return $addColumnsArray;
    }

}