<?php

declare(strict_types=1);

namespace App\BasicRum\InternalData;

interface InternalDataInterface
{

    public function isApplicable(array $options) : bool;

}