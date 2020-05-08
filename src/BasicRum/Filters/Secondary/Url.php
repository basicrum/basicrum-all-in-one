<?php

declare(strict_types=1);

namespace App\BasicRum\Filters\Secondary;

class Url extends AbstractFilter
{
    public function getSecondaryTableName(): string
    {
        return 'navigation_timings_urls';
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
        return 'navigation_timings';
    }

    public function getPrimarySearchFieldName(): string
    {
        return 'url_id';
    }
}
