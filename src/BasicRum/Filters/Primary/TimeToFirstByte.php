<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

class TimeToFirstByte
    extends AbstractFilter
{

    public function getPrimaryEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimarySearchFieldName() : string
    {
        return 'firstByte';
    }

}