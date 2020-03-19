<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class TotalJsCompressedSize
    implements \App\BasicRum\Report\SelectableInterface
{

    public function getSelectDataFieldName(): string
    {
        return 'total_js_compressed_size';
    }

    public function getSelectTableName() : string
    {
        return 'navigation_timings';
    }

}