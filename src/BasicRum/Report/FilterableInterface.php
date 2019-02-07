<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface FilterableInterface
{

    public function setCondition(string $condition);

    public function getCondition() : string;

    public function setSearchValue(string $searchValue);

    public function getSearchValue() : string;

}