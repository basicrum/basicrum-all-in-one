<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;


class Count
    implements MainDataInterface
{

    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /**
     * Percentile constructor.
     * @param string $tableName
     * @param string $fieldName
     */
    public function __construct(
        string $tableName,
        string $fieldName
    )
    {
        $this->tableName  = $tableName;
        $this->fieldName  = $fieldName;
    }

    /**
     * @param string $where
     * @param array $limitWhere
     * @return string
     */
    public function getCountSql(string $where, array $limitWhere) : string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        return
            "SELECT COUNT(`{$this->tableName}`.`{$this->fieldName}`) as `cnt`
FROM navigation_timings
WHERE {$limitWhereStr} AND {$where}";
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

        return ['count' => $res[0]['cnt']];
    }

}

