<?php

declare(strict_types=1);

namespace App\BasicRum\InternalData;

class DataField implements InternalDataInterface
{
    public function isApplicable(array $options): bool
    {
        return true;
    }
}
