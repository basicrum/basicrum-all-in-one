<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;

class Count implements MainDataInterface
{
    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /**
     * Percentile constructor.
     */
    public function __construct(
        string $tableName,
        string $fieldName
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
    }

    public function getCountSql(string $where, array $limitWhere): string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        if (!empty($where)) {
            $where = ' AND '.$where;
        }

        return
            "SELECT COUNT(`{$this->tableName}`.`{$this->fieldName}`) as `cnt`
FROM navigation_timings
WHERE {$limitWhereStr} {$where}";
    }

    /**
     * @param $connection
     */
    public function retrieve($connection, string $where, array $limitWhere): array
    {
        $sql = $this->getCountSql($where, $limitWhere);

        $res = $connection->fetchAll($sql);

        return ['count' => $res[0]['cnt']];
    }

    public function getCacheKeyPart(): string
    {
        return 'count_'.md5(
                $this->tableName.
                $this->fieldName
            );
    }
}
