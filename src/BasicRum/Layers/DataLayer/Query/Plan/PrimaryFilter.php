<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class PrimaryFilter
{

    private $primaryEntityName;

    private $primarySearchFieldName;

    private $condition;

    public function __construct(
        string $primaryEntityName,
        string $primarySearchFieldName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition
    )
    {
        $this->primaryEntityName      = $primaryEntityName;
        $this->primarySearchFieldName = $primarySearchFieldName;
        $this->condition              = $condition;
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
    public function getCondition() : \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
    {
        return $this->condition;
    }

}