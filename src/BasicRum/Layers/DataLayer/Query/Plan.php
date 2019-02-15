<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

class Plan
{

    /** @var string */
    private $mainEntityName;

    /** @var array */
    private $secondaryFilters = [];

    /** @var array */
    private $primaryFilters   = [];

    /** @var array */
    private $selects         = [];

    public function __construct(string $mainEntityName)
    {
        $this->mainEntityName = $mainEntityName;
    }

    /**
     * @param string $primaryEntityName
     * @param string $primarySearchFieldName
     * @param ConditionInterface $prefetchCondition
     * @param SelectInterface $prefetchSelect
     * @param string $mainCondition
     * @return Plan
     */
    public function addSecondaryFilter(
        string $primaryEntityName,
        string $primarySearchFieldName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $prefetchCondition,
        \App\BasicRum\Layers\DataLayer\Query\SelectInterface $prefetchSelect,
        string $mainCondition
    ) : self
    {
        $this->secondaryFilters[] = new Plan\SecondaryFilter(
                $primaryEntityName,
                $primarySearchFieldName,
                $prefetchCondition,
                $prefetchSelect,
                $mainCondition
            );

        return $this;
    }

    /**
     * @param string $entityName
     * @param string $filterField
     * @param string $condition
     * @return Plan
     */
    public function addFilter(
        string $entityName,
        string $filterField,
        string $condition
    ) : self
    {
        $this->primaryFilters[] = [
            'entityName'  => $entityName,
            'filterField' => $filterField,
            'condition'   => $condition
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function releasePlan() : array
    {
        return [
            'main_entity_name' => $this->mainEntityName,
            'select' => [
                [$this->mainEntityName => 'pageViewId']
            ],
            'where'  => [
                'primaryFilters'   => $this->primaryFilters,
                'secondaryFilters' => $this->secondaryFilters
            ]
        ];
    }

}