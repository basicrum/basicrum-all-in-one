<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;

class DataRows implements MainDataInterface
{
    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldNames;

    /**
     * Percentile constructor.
     */
    public function __construct(
        string $tableName,
        array $fieldNames
    ) {
        $this->tableName = $tableName;
        $this->fieldNames = $fieldNames;
    }

    public function getCountSql(string $where, array $limitWhere): string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        if (!empty($where)) {
            $where = ' AND '.$where;
        }

        return
            "SELECT {$this->generateSelectClauseFields()}
FROM navigation_timings
WHERE {$limitWhereStr} {$where}";
    }

    /**
     * @return string
     */
    private function generateSelectClauseFields()
    {
        $selectFieldsArray = [];

        foreach ($this->fieldNames as $fieldName) {
            $selectFieldsArray[] = " `{$this->tableName}`.`{$fieldName}` as `{$fieldName}`";
        }

        return implode(', ', $selectFieldsArray);
    }

    /**
     * @param $connection
     */
    public function retrieve($connection, string $where, array $limitWhere): array
    {
        $sql = $this->getCountSql($where, $limitWhere);

        $res = $connection->fetchAll($sql);

        return ['data_rows' => $res];
    }

    public function getCacheKeyPart(): string
    {
        return 'data_rows_'.md5(
                $this->tableName.
                implode('-', $this->fieldNames)
            );
    }
}
