<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class NumberJsFiles implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 'number_js_files';
    }

    public function getSelectTableName(): string
    {
        return 'rum_data_flat';
    }
}
