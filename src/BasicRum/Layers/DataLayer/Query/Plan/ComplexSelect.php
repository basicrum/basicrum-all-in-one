<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class ComplexSelect
{

    /** @var string */
    private $primarySelectEntityName;

    /** @var string */
    private $primaryKeyFieldName;

    /** @var string */
    private $secondarySelectEntityName;

    /** @var string */
    private $secondaryKeyFieldName;

    /** @var array */
    private $secondarySelectDataFieldNames;

    public function __construct(
        string $primarySelectEntityName,
        string $primaryKeyFieldName,
        string $secondarySelectEntityName,
        string $secondaryKeyFieldName,
        array $secondarySelectDataFieldNames
    )
    {
        $this->primarySelectEntityName       = $primarySelectEntityName;
        $this->primaryKeyFieldName           = $primaryKeyFieldName;
        $this->secondarySelectEntityName     = $secondarySelectEntityName;
        $this->secondaryKeyFieldName         = $secondaryKeyFieldName;
        $this->secondarySelectDataFieldNames = $secondarySelectDataFieldNames;
    }

    /**
     * @return string
     */
    public function getPrimarySelectEntityName() : string
    {
        return $this->primarySelectEntityName;
    }

    /**
     * @return string
     */
    public function getPrimaryKeyFieldName() : string
    {
        return $this->primaryKeyFieldName;
    }

    /**
     * @return string
     */
    public function getSecondarySelectEntityName() : string
    {
        return $this->getSecondarySelectEntityName();
    }

    /**
     * @return string
     */
    public function getSecondaryKeyFieldName() : string
    {
        return $this->secondaryKeyFieldName;
    }

    /**
     * @return array
     */
    public function getSecondarySelectDataFieldNames() : array
    {
        return $this->secondarySelectDataFieldNames;
    }

}