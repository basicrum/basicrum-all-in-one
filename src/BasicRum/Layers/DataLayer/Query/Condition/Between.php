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


}