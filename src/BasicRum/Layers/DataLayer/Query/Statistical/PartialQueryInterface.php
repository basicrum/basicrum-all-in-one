<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query\Statistical;

interface PartialQueryInterface
{

    public function getPartialQuery() : string;

}