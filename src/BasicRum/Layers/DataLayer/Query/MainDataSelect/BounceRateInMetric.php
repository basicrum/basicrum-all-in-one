<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\MainDataSelect;

class BounceRateInMetric
    implements MainDataInterface
{

    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /** @var int */
    private $percentile;

    /**
     * Percentile constructor.
     * @param string $tableName
     * @param string $fieldName
     * @param int $percentile
     */
    public function __construct(
        string $tableName,
        string $fieldName,
        int $percentile
    )
    {
        $this->tableName = $tableName;
        $this->fieldName  = $fieldName;
        $this->percentile = $percentile;
    }

    /**
     * @param string $where
     * @return string
     */
    public function getBouncedBuckets(string $where) : string
    {
        return

"SELECT floor(first_byte/200)*200 AS bin_floor, COUNT(*)
FROM navigation_timings
WHERE page_view_id IN(
	SELECT visits_overview.first_page_view_id 
    FROM visits_overview
    WHERE visits_overview.first_page_view_id >= 163140 AND visits_overview.first_page_view_id <= 165312
		AND visits_overview.page_views_count = 1
		AND visits_overview.first_page_view_id IN (
			SELECT page_view_id
			from navigation_timings
			WHERE navigation_timings.page_view_id >= 163140
				AND navigation_timings.page_view_id <= 165312
				AND navigation_timings.device_type_id = '1'
				AND navigation_timings.url_id  IN(4396)
    )
) AND page_view_id >= 163140 AND page_view_id <= 165312
GROUP BY 1
ORDER BY 1";
    }

    /**
     * @param string $where
     * @return string
     */
    public function getAllBuckets(string $where) : string
    {
        return

            "SELECT floor(first_byte/200)*200 AS bin_floor, COUNT(*)
FROM navigation_timings
WHERE page_view_id IN(
	SELECT visits_overview.first_page_view_id 
    FROM visits_overview
    WHERE visits_overview.first_page_view_id >= 163140 AND visits_overview.first_page_view_id <= 165312
		AND visits_overview.first_page_view_id IN (
			SELECT page_view_id
			from navigation_timings
			WHERE navigation_timings.page_view_id >= 163140
				AND navigation_timings.page_view_id <= 165312
				AND navigation_timings.device_type_id = '1'
				AND navigation_timings.url_id  IN(4396)
    )
) AND page_view_id >= 163140 AND page_view_id <= 165312
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
        $sql = $this->getPercentileSql($where, $limitWhere);

        $res = $connection->fetchAll($sql);

        return $res;
    }

}

