<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

interface ConditionInterface
{

    public function getWhere() : string;

    public function getParams() : array;

}