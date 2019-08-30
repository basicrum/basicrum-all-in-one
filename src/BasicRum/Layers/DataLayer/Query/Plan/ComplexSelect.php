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
    )
    {
        $this->primarySelectTableName       = $primarySelectTableName;
        $this->primaryKeyFieldName           = $primaryKeyFieldName;
        $this->secondarySelectTableName     = $secondarySelectTableName;
        $this->secondaryKeyFieldName         = $secondaryKeyFieldName;
        $this->secondarySelectDataFieldNames = $secondarySelectDataFieldNames;
    }

    /**
     * @return string
     */
    public function getPrimarySelectTableName() : string
    {
        return $this->primarySelectTableName;
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
    public function getSecondarySelectTableName() : string
    {
        return $this->secondarySelectTableName;
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