<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface ComplexSelectableInterface
{
    public function getSecondarySelectTableName(): string;

    public function getSecondarySelectDataFieldNames(): array;

    public function getSecondaryKeyFieldName(): string;

    public function getPrimarySelectTableName(): string;

    public function getPrimaryKeyFieldName(): string;
}
