<?php

declare(strict_types=1);

namespace App\BasicRum\Report\Filter;

interface FilterInterface
{

    public function getFilterLabel();

    public function attachTo(
        $value,
        string $condition,
        \Doctrine\ORM\QueryBuilder $queryBuilder
    );

    public function getInternalIdentifier();

}