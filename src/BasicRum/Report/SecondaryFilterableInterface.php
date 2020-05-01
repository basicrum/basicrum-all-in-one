<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface SecondaryFilterableInterface
{
    public function __construct(
        string $condition,
        string $searchValue
    );

    public function getCondition(): string;

    public function getSearchValue(): string;

    public function getSecondaryTableName(): string;

    public function getSecondarySearchFieldName(): string;

    public function getSecondaryKeyFieldName(): string;

    public function getPrimaryTableName(): string;

    public function getPrimarySearchFieldName(): string;

    public function getSchema(): ?array;
}
