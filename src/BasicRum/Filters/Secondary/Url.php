<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class Url extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'rum_data_urls';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'id';
    }

    public function getSecondarySearchFieldName(): string
    {
        return 'url';
    }

    public function getPrimaryTableName(): string
    {
        return 'rum_data_flat';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'url_id';
    }
}
