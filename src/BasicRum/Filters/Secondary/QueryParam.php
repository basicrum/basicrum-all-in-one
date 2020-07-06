<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class QueryParam extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'rum_data_flat_query_params';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'rum_data_id';
    }

    public function getSecondarySearchFieldName(): string
    {
        return 'query_params';
    }

    public function getPrimaryTableName(): string
    {
        return 'rum_data_flat';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'rum_data_id';
    }
}
