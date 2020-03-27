<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class SecondaryFilter
{
    private $primaryTableName;

    private $primarySearchFieldName;

    private $secondaryTableName;

    private $prefetchCondition;

    private $prefetchSelect;

    private $mainCondition;

    public function __construct(
        string $primaryTableName,
        string $primarySearchFieldName,
        string $secondaryTableName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $prefetchCondition,
        \App\BasicRum\Layers\DataLayer\Query\SelectInterface $prefetchSelect,
        string $mainCondition
    ) {
        $this->primaryTableName = $primaryTableName;
        $this->primarySearchFieldName = $primarySearchFieldName;
        $this->secondaryTableName = $secondaryTableName;
        $this->prefetchCondition = $prefetchCondition;
        $this->prefetchSelect = $prefetchSelect;
        $this->mainCondition = $mainCondition;
    }

    public function getPrimaryTableName(): string
    {
        return $this->primaryTableName;
    }

    public function getPrimarySearchFieldName(): string
    {
        return $this->primarySearchFieldName;
    }

    public function getSecondaryTableName(): string
    {
        return $this->secondaryTableName;
    }

    public function getPrefetchCondition(): \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
    {
        return $this->prefetchCondition;
    }

    public function getPrefetchSelect(): \App\BasicRum\Layers\DataLayer\Query\SelectInterface
    {
        return $this->prefetchSelect;
    }

    public function getMainCondition(): string
    {
        return $this->mainCondition;
    }
}
