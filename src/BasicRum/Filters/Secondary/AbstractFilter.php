<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

abstract class AbstractFilter implements
    \App\BasicRum\Report\SecondaryFilterableInterface
{

    protected $condition;

    protected $searchValue;

    public function __construct(string $condition, string $searchValue)
    {
        $this->condition = $condition;
        $this->searchValue = $searchValue;
    }

    /**
     * @return string
     */
    public function getCondition() : string
    {
        return $this->condition;
    }

    public function getSearchValue() : string
    {
        return $this->searchValue;
    }

}