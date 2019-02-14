<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Metric;

class DeviceType
    extends AbstractFilter
{

    public function getSecondaryEntityName() : string
    {
        return 'NavigationTimingsUserAgents';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'id';
    }

    public function getSecondarySearchFieldName() : string
    {
        return 'deviceType';
    }

    public function getPrimaryEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimarySearchFieldName() : string
    {
        return 'userAgentId';
    }

}