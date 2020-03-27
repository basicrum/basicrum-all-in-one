<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Select;

class Count implements \App\BasicRum\Layers\DataLayer\Query\SelectInterface
{
    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    public function __construct(
        string $tableName,
        string $fieldName
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
    }

    public function getFields(): array
    {
        return ["COUNT({$this->tableName}.{$this->fieldName})"];
    }
}
