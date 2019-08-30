<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Statistical;

class Percentile
    implements PartialQueryInterface
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
     * @return string
     */
    public function getPartialQuery() : string
    {
        return

"SELECT DISTINCT first_value({$this->tableName}.{$this->fieldName}) OVER (ORDER BY CASE WHEN p <= 0.{$this->percentile} THEN p END DESC) x
FROM (
    SELECT
    {$this->tableName}.{$this->fieldName},
    percent_rank() OVER (ORDER BY {$this->tableName}.{$this->fieldName}) p
  FROM navigation_timings
  {{SUB_WHERE}}
) t;";
    }

}

