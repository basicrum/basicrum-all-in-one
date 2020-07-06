<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

class TimeToFirstByte extends AbstractFilter
{
    public function getPrimaryTableName(): string
    {
        return 'rum_data_flat';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'first_byte';
    }
}
