<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Condition;

class GreaterThanOrEqual
    implements \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
{

    /** @var string */
    private $entityName;

    /** @var string */
    private $fieldName;

    /** @var string */
    private $value;

    public function __construct(
        string $entityName,
        string $fieldName,
        string $value
    )
    {
        $this->entityName = $entityName;
        $this->fieldName  = $fieldName;
        $this->value      = $value;
    }

    /**
     * @return string
     */
    public function getWhere() : string
    {
        return $this->entityName . "." . $this->fieldName . " >= " . ":" . $this->_getValueKey();
    }

    /**
     * @return string
     */
    private function _getValueKey() : string
    {
        return $this->entityName . "_" . $this->fieldName . '_value';
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