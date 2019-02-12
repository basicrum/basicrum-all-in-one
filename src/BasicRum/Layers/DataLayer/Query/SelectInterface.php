<?php

declare(strict_types=1);

namespace App\BasicRum\Layers\DataLayer\Query;

interface SelectInterface
{

    public function getFields() : array;

}