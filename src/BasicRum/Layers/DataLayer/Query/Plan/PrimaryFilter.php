<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class PrimaryFilter
{

    private $primaryTableName;

    private $primarySearchFieldName;

    private $condition;

    public function __construct(
        string $primaryTableName,
        string $primarySearchFieldName,
        \App\BasicRum\Layers\DataLayer\Query\ConditionInterface $condition
    )
    {
        $this->primaryTableName      = $primaryTableName;
        $this->primarySearchFieldName = $primarySearchFieldName;
        $this->condition              = $condition;
    }

    /**
     * @return string
     */
    public function getPrimaryTableName() : string
    {
        return $this->primaryTableName;
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