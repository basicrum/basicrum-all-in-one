<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Condition;

class Between
    implements \App\BasicRum\Layers\DataLayer\Query\ConditionInterface
{

    /** @var string */
    private $entityName;

    /** @var string */
    private $fieldName;

    /** @var string */
    private $leftPart;

    /** @var string */
    private $rightPart;

    public function __construct(
        string $entityName,
        string $fieldName,
        string $leftPart,
        string $rightPart
    )
    {
        $this->entityName = $entityName;
        $this->fieldName  = $fieldName;
        $this->leftPart   = $leftPart;
        $this->rightPart  = $rightPart;
    }

    /**
     * @return string
     */
    public function getWhere() : string
    {
        return $this->entityName . "." . $this->fieldName . " BETWEEN " . ":" . $this->_leftPartName() . " AND :" . $this->_rightPartName();
    }

    /**
     * @return string
     */
    private function _leftPartName() : string
    {
        return $this->entityName . "_" . $this->fieldName . '_left';
    }

    /**
     * @return string
     */
    private function _rightPartName() : string
    {
        return $this->entityName . "_" . $this->fieldName . '_right';
    }

    /**
     * @return array
     */
    public function getParams() : array
    {
        return [
            $this->_leftPartName()  => $this->leftPart,
            $this->_rightPartName() => $this->rightPart,
        ];
    }

}