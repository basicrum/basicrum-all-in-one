<?php

declare(strict_types=1);

namespace App\BasicRum\BusinessMetrics;

class BounceRate implements \App\BasicRum\Report\ComplexSelectableInterface
{
    public function getSecondarySelectDataFieldNames(): array
    {
        return [
            'page_views_count',
            'first_page_view_id',
            'guid',
        ];
    }

    public function getSecondarySelectTableName(): string
    {
        return 'visits_overview';
    }

    public function getSecondaryKeyFieldName(): string
    {
        return 'first_page_view_id';
    }

    public function getPrimarySelectTableName(): string
    {
        return 'navigation_timings';
    }

    public function getPrimaryKeyFieldName(): string
    {
        return 'page_view_id';
    }
}
