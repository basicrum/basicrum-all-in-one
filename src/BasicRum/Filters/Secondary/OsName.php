<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class OsName
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
        return 'osName';
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