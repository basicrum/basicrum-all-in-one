<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Condition;

class GreaterThanOrEqual
    implements \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
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
    )
    {
        $this->tableName = $tableName;
        $this->fieldName  = $fieldName;
        $this->value      = $value;
    }

    /**
     * @return string
     */
    public function getWhere() : string
    {
        return $this->tableName . "." . $this->fieldName . " >= " . ":" . $this->_getValueKey();
    }

    /**
     * @return string
     */
    private function _getValueKey() : string
    {
        return $this->tableName . "_" . $this->fieldName . '_value';
    }

    /**
     * @return array
     */
    public function getParams() : array
    {
        return [
            $this->_getValueKey()  => $this->value,
        ];
    }

}