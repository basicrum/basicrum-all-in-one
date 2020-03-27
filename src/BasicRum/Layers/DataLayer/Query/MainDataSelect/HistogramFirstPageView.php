<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;

class HistogramFirstPageView implements MainDataInterface
{
    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /** @var int */
    private $bucketSize;

    /**
     * Percentile constructor.
     */
    public function __construct(
        string $tableName,
        string $fieldName,
        int $bucketSize
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->bucketSize = $bucketSize;
    }

    public function getBucketsSql(string $where, array $limitWhere): string
    {
        $limitWhereStr = implode(' AND ', $limitWhere);

        $visitsOverviewLimit = str_replace(
            'navigation_timings.page_view_id',
            'visits_overview.first_page_view_id',
            $limitWhereStr
        );

        if (!empty($where)) {
            $where = ' AND '.$where;
        }

        return

            "SELECT floor(first_paint/$this->bucketSize)*$this->bucketSize AS bin_floor, COUNT(*)
FROM navigation_timings
WHERE 
  {$limitWhereStr} AND
  page_view_id IN
  (
	SELECT visits_overview.first_page_view_id 
    FROM visits_overview
    WHERE {$visitsOverviewLimit}
		AND visits_overview.page_views_count = 1
		AND visits_overview.first_page_view_id IN
		  (
			SELECT page_view_id
			from navigation_timings
			WHERE {$limitWhereStr} {$where} AND {$this->tableName}.{$this->fieldName} > 0
		  )
  )
  
GROUP BY 1
ORDER BY 1";
    }

    /**
     * @param $connection
     */
    public function retrieve($connection, string $where, array $limitWhere): array
    {
        $sql = $this->getBucketsSql($where, $limitWhere);

        return ['all_buckets' => $this->flattenBuckets($connection->fetchAll($sql))];
    }

    private function flattenBuckets(array $buckets): array
    {
        $flatten = [];

        foreach ($buckets as $bucket) {
            $flatten[$bucket['bin_floor']] = $bucket['COUNT(*)'];
        }

        return $flatten;
    }

    public function getCacheKeyPart(): string
    {
        return 'histogram_first_page_view'.md5(
                $this->tableName.
                $this->fieldName.
                $this->bucketSize
            );
    }
}
