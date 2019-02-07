<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Metric;

abstract class AbstractFilter implements
    \App\BasicRum\Report\FilterableInterface,
    \App\BasicRum\Report\SelectableInterface
{

    protected $condition;

    protected $searchValue;

    public function setCondition(string $condition) : self
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * @return string
     */
    public function getCondition() : string
    {
        return $this->condition;
    }

    public function setSearchValue(string $searchValue) : self
    {
        $this->searchValue = $searchValue;

        return $this;
    }

    public function getSearchValue() : string
    {
        return $this->searchValue;
    }

}