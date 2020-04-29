<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

abstract class AbstractFilter implements \App\BasicRum\Report\PrimaryFilterableInterface
{
    protected $condition;

    protected $searchValue;

    public function __construct(?string $condition = null, ?string $searchValue = null)
    {
        $this->condition = $condition;
        $this->searchValue = $searchValue;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getSearchValue(): string
    {
        return $this->searchValue;
    }
}
