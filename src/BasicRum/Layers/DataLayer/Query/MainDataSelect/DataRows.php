<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;


class DataRows
    implements MainDataInterface
{

    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldNames;

    /**
     * Percentile constructor.
     * @param string $tableName
     * @param array $fieldNames
     */
    public function __construct(
        string $tableName,
        array $fieldNames
    )
    {
        $this->tableName  = $tableName;
        $this->fieldNames = $fieldNames;
    }

    /**
     * @param string $where
     * @param array $limitWhere
     * @return string
     */
    public function getCountSql(string $where, array $limitWhere) : string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        if (!empty($where)) {
            $where = ' AND ' . $where;
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
     * @param string $where
     * @param array $limitWhere
     * @return array
     */
    public function retrieve($connection, string $where, array $limitWhere) : array
    {
        $sql = $this->getCountSql($where, $limitWhere);

        $res = $connection->fetchAll($sql);

        return ['data_rows' => $res];
    }

}

