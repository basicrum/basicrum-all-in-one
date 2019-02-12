<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

class Plan
{

    /** @var string */
    private $mainEntityName;

    /** @var array */
    private $prefetchFilters = [];

    /** @var array */
    private $normalFilters   = [];

    /** @var array */
    private $selects         = [];

    public function __construct(string $mainEntityName)
    {
        $this->mainEntityName = $mainEntityName;
    }

    /**
     * @param string $entityName
     * @param string $filterField
     * @param ConditionInterface $prefetchCondition
     * @param SelectInterface $prefetchSelect
     * @param string $mainCondition
     * @return Plan
     */
    public function addPrefetchFilter(
        string $entityName,
        string $filterField,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $prefetchCondition,
        \App\BasicRum\Layers\DataLayer\Query\SelectInterface $prefetchSelect,
        string $mainCondition
    ) : self
    {
        $this->prefetchFilters[] = [
            'entityName'        => $entityName,
            'filterField'       => $filterField,
            'prefetchCondition' => $prefetchCondition,
            'prefetchSelect'    => $prefetchSelect,
            'mainCondition'     => $mainCondition
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
                'normal'   => $this->normalFilters,
                'prefetch' => $this->prefetchFilters
            ]
        ];
    }

}