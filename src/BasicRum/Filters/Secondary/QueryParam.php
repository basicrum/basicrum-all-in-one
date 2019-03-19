<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class QueryParam
    extends AbstractFilter
{

    public function getSecondaryEntityName() : string
    {
        return 'NavigationTimingsQueryParams';
    }

    public function getSecondaryKeyFieldName() : string
    {
        return 'pageViewId';
    }

    public function getSecondarySearchFieldName() : string
    {
        return 'queryParams';
    }

    public function getPrimaryEntityName() : string
    {
        return 'NavigationTimings';
    }

    public function getPrimarySearchFieldName() : string
    {
        return 'pageViewId';
    }

}