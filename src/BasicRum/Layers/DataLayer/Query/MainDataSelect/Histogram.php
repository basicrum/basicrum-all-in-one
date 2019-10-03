<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;


class Histogram
    implements MainDataInterface
{

    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /** @var int */
    private $bucketSize;

    /**
     * Percentile constructor.
     * @param string $tableName
     * @param string $fieldName
     * @param int $bucketSize
     */
    public function __construct(
        string $tableName,
        string $fieldName,
        int $bucketSize
    )
    {
        $this->tableName  = $tableName;
        $this->fieldName  = $fieldName;
        $this->bucketSize = $bucketSize;
    }

    /**
     * @param string $where
     * @param array $limitWhere
     * @return string
     */
    public function getBucketsSql(string $where, array $limitWhere) : string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        if (!empty($where)) {
            $where = ' AND ' . $where;
        }

        return

            "SELECT floor({$this->fieldName}/{$this->bucketSize})*{$this->bucketSize} AS bin_floor, COUNT(*)
FROM navigation_timings
WHERE {$limitWhereStr} {$where}
  
GROUP BY 1
ORDER BY 1";
    }

    /**
     * @param $connection
     * @param string $where
     * @param array $limitWhere
     * @return array
     */
    public function retrieve($connection, string $where, array $limitWhere) : array
    {
        $sql = $this->getBucketsSql($where, $limitWhere);

        return ['all_buckets' => $this->flattenBuckets($connection->fetchAll($sql))];
    }

    /**
     * @param array $buckets
     * @return array
     */
    private function flattenBuckets(array $buckets) : array
    {
        $flatten = [];

        foreach ($buckets as $bucket) {
            $flatten[$bucket['bin_floor']] = $bucket['COUNT(*)'];
        }

        return $flatten;
    }

}

