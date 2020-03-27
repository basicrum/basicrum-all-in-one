<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Condition;

class LessThanOrEqual implements \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
{
    /** @var string */
    private $tableName;

    /** @var string */
    private $fieldName;

    /** @var string */
    private $value;

    public function __construct(
        string $tableName,
        string $fieldName,
        string $value
    ) {
        $this->tableName = $tableName;
        $this->fieldName = $fieldName;
        $this->value = $value;
    }

    public function getWhere(): string
    {
        return $this->tableName.'.'.$this->fieldName.' <= '.':'.$this->_getValueKey();
    }

    private function _getValueKey(): string
    {
        return $this->tableName.'_'.$this->fieldName.'_value';
    }

    public function getParams(): array
    {
        return [
            $this->_getValueKey() => $this->value,
        ];
    }
}
