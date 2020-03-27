<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Primary;

class DeviceType extends AbstractFilter
{
    public function getPrimaryTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'device_type_id';
    }
}
