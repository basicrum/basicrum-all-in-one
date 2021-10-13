<?php

declare(strict_types=1);

namespace App\BasicRum\Metrics\Simple\Technical\CumulativeLayoutShift;

use App\BasicRum\Metrics\Interfaces\ReaderHintInterface;

class ReaderHint implements ReaderHintInterface
{
    public function getFieldName(): string
    {
        return 'cumulative_layout_shift';
    }

    public function getTabledName(): string
    {
        return 'rum_data_flat';
    }
}
