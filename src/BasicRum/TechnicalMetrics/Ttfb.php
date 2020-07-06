<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class Ttfb implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 'ttfb';
    }

    public function getSelectTableName(): string
    {
        return 'rum_data_flat';
    }
}
