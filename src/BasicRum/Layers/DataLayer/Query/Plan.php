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
    private $selects          = [];

    private $complexSelects   = [];

    public function __construct(string $mainEntityName)
    {
        $this->selects[] = new Plan\Select(
            $mainEntityName,
            'pageViewId'
        );


        $this->mainEntityName = $mainEntityName;
    }

    public function addSelect(
        string $entityName,
        string $dataFieldName
    ) : self
    {
        $this->selects[] = new Plan\Select(
            $entityName,
            $dataFieldName
        );

        return $this;
    }

    /**
     * @param string $primarySelectEntityName
     * @param string $primaryKeyFieldName
     * @param string $secondarySelectEntityName
     * @param string $secondaryKeyFieldName
     * @param array $secondarySelectDataFieldNames
     * @return Plan
     */
    public function addComplexSelect(
        string $primarySelectEntityName,
        string $primaryKeyFieldName,
        string $secondarySelectEntityName,
        string $secondaryKeyFieldName,
        array $secondarySelectDataFieldNames
    ) : self
    {
        $this->complexSelects[] = new Plan\ComplexSelect(
            $primarySelectEntityName,
            $primaryKeyFieldName,
            $secondaryKeyFieldName,
            $secondarySelectEntityName,
            $secondarySelectDataFieldNames
        );

        return $this;
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
     * @param string $primaryEntityName
     * @param string $primarySearchFieldName
     * @param string $mainCondition
     * @return Plan
     */
    public function addPrimaryFilter(
        string $primaryEntityName,
        string $primarySearchFieldName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition
    ) : self
    {
        $this->primaryFilters[] = new Plan\PrimaryFilter(
                $primaryEntityName,
                $primarySearchFieldName,
                $condition
            );

        return $this;
    }

    /**
     * @return array
     */
    public function releasePlan() : array
    {
        return [
            'main_entity_name' => $this->mainEntityName,
            'selects' => $this->selects,
            'where'  => [
                'primaryFilters'   => $this->primaryFilters,
                'secondaryFilters' => $this->secondaryFilters
            ]
        ];
    }

}