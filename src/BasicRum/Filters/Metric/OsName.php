<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Metric;

class OsName
    extends AbstractFilter
{

    public function getDataField() : string
    {
        return 'os_name';
    }

public function getEntity() : string
    {
        return 'NavigationTimingsUserAgents';
    }

    public function getRelatedEntity() : string
    {
        return 'id';
    }

    public function getKeyField() : string
    {
        return 'NavigationTimings';
    }

    public function getRelatedKeyField() : string
    {
        return 'user_agent_id';
    }

}