<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class QueryParam extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'navigation_timings_query_params';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'page_view_id';
    }

    public function getSecondarySearchFieldName(): string
    {
        return 'query_params';
    }

    public function getPrimaryTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'page_view_id';
    }
}
