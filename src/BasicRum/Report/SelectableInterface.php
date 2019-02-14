<?php

declare(strict_types=1);

namespace App\BasicRum\Report;

interface SelectableInterface
{

    public function getSelectDataField() : string;

    public function getSelectEntityName() : string;

}