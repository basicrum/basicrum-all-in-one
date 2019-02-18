<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface ComplexSelectableInterface
{

    public function getSecondarySelectEntityName() : string;

    public function getSecondarySelectDataFieldNames() : array;

    public function getSecondaryKeyFieldName() : string;

    public function getPrimarySelectEntityName() : string;

    public function getPrimaryKeyFieldName() : string;

}