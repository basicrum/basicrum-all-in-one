<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class ComplexSelect
{
    /** @var string */
    private $primarySelectTableName;

    /** @var string */
    private $primaryKeyFieldName;

    /** @var string */
    private $secondarySelectTableName;

    /** @var string */
    private $secondaryKeyFieldName;

    /** @var array */
    private $secondarySelectDataFieldNames;

    public function __construct(
        string $primarySelectTableName,
        string $primaryKeyFieldName,
        string $secondarySelectTableName,
        string $secondaryKeyFieldName,
        array $secondarySelectDataFieldNames
    ) {
        $this->primarySelectTableName = $primarySelectTableName;
        $this->primaryKeyFieldName = $primaryKeyFieldName;
        $this->secondarySelectTableName = $secondarySelectTableName;
        $this->secondaryKeyFieldName = $secondaryKeyFieldName;
        $this->secondarySelectDataFieldNames = $secondarySelectDataFieldNames;
    }

    public function getPrimarySelectTableName(): string
    {
        return $this->primarySelectTableName;
    }

    public function getPrimaryKeyFieldName(): string
    {
        return $this->primaryKeyFieldName;
    }

    public function getSecondarySelectTableName(): string
    {
        return $this->secondarySelectTableName;
    }

    public function getSecondaryKeyFieldName(): string
    {
        return $this->secondaryKeyFieldName;
    }

    public function getSecondarySelectDataFieldNames(): array
    {
        return $this->secondarySelectDataFieldNames;
    }
}
