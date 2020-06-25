<?php

declare(strict_types=1);

namespace App\BasicRum\TechnicalMetrics;

class TResp implements \App\BasicRum\Report\SelectableInterface
{
    public function getSelectDataFieldName(): string
    {
        return 't_resp';
    }

    public function getSelectTableName(): string
    {
        return 'rum_data_flat';
    }
}
