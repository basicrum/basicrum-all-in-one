<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class DownloadTime implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 'download_time';
    }

    public function getSelectTableName(): string
    {
        return 'rum_data_flat';
    }
}
