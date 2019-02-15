<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class SecondaryFilter
{

    private $primaryEntityName;

    private $primarySearchFieldName;

    private $prefetchCondition;

    private $prefetchSelect;

    private $mainCondition;

    public function __construct(
        string $primaryEntityName,
        string $primarySearchFieldName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $prefetchCondition,
        \App\BasicRum\Layers\DataLayer\Query\SelectInterface $prefetchSelect,
        string $mainCondition
    )
    {
        $this->primaryEntityName      = $primaryEntityName;
        $this->primarySearchFieldName = $primarySearchFieldName;
        $this->prefetchCondition      = $prefetchCondition;
        $this->prefetchSelect         = $prefetchSelect;
        $this->mainCondition          = $mainCondition;
    }

    /**
     * @return string
     */
    public function getPrimaryEntityName() : string
    {
        return $this->primaryEntityName;
    }

    /**
     * @return string
     */
    public function getPrimarySearchFieldName() : string
    {
        return $this->primarySearchFieldName;
    }

    /**
     * @return \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
     */
    public function getPrefetchCondition() : \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
    {
        return $this->prefetchCondition;
    }

    /**
     * @return \App\BasicRum\Layers\DataLayer\Query\SelectInterface
     */
    public function getPrefetchSelect() : \App\BasicRum\Layers\DataLayer\Query\SelectInterface
    {
        return $this->prefetchSelect;
    }

    /**
     * @return string
     */
    public function getMainCondition() : string
    {
        return $this->mainCondition;
    }

}