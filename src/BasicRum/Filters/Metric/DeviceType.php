<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Metric;

class DeviceType
    extends AbstractFilter
{

    public function getDataField() : string
    {
        return 'device_type';
    }

    public function getEntity() : string
    {
        return 'NavigationTimingsUserAgents';
    }

    public function getRelatedEntity() : string
    {
        return 'NavigationTimings';
    }

    public function getKeyField() : string
    {
        return 'id';
    }

    public function getRelatedKeyField() : string
    {
        return 'user_agent_id';
    }

}