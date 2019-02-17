<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Plan;

class Select
{

    /** @var string */
    private $entityName;

    /** @var string */
    private $dataFieldName;

    public function __construct(
        string $entityName,
        string $dataFieldName
    )
    {
        $this->entityName      = $entityName;
        $this->dataFieldName   = $dataFieldName;
    }

    /**
     * @return string
     */
    public function getEntityName() : string
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getDataFieldName() : string
    {
        return $this->dataFieldName;
    }

}